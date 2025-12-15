new Vue({
	el:'#usuarios',
	data(){
		return{
			data:{
				id_usuario:'',
				login:'',
				password:'',
				name:'',
				status:''
			},
			permisos: {
				id_usuario: '',
				nombre_usuario: '',
				lista: []
			},
			modulo:'',
			list:true,
			form:false,
			permisosView:false
		}
	},
	created(){
		this.listUsers();
		this.activos();
	},
	methods:{
		submit:function() {
			// Validaciones
			if (!this.data.login || !this.data.name || this.data.status === '') {
				this.sweetalert2('ERROR', 'Por favor complete todos los campos', 'error');
				return;
			}
			
			if (!this.data.id_usuario && !this.data.password) {
				this.sweetalert2('ERROR', 'La contraseña es requerida para nuevos usuarios', 'error');
				return;
			}
			
			const endpoint = this.data.id_usuario ? 'update' : 'new';
			
			axios.post(`controlador/usuarios.php?op=${endpoint}`, this.data)
				.then(resp =>{
					this.sweetalert2(resp.data.tittle, resp.data.message, resp.data.type);
					if (resp.data.type === 'success') {
						this.cancel();
					}
			}).catch(error => {
				this.sweetalert2('ERROR', 'Error al guardar usuario', 'error');
				console.error(error);
			});
		},
		
		listUsers(){
			const thisJq = this;
			$(function() {
				$("#list").DataTable({
					"responsive": true,
					"autoWidth": false,
					"lengthMenu": [[50, 100, -1], [50, 100, "All"]],
					"aProcessing": true,
					"aServerSide": true,
					"ajax": {
						"url": 'controlador/usuarios.php?op=listUsers',
						"type": "POST",
						"error": function(e) {console.log(e);}
					},
					"bDestroy": true,
					"iDisplayLength": 25,
					"order": [[1, "asc"]]
				});
			});
		},
		
		cancel(){
			this.list = true;
			this.form = false;
			this.permisosView = false;
			this.resetInput();
			this.listUsers();
		},
		
		activos() {
			const thisJq = this;
			$(function () {
				// Botón para deshabilitar/habilitar usuario
				$(document).on("click", "#disabled", function() {
					let id = $(this).val();
					
					Swal.fire({
						title: '¿Estás seguro?',
						text: "¿Deseas cambiar el estado de este usuario?",
						icon: 'warning',
						showCancelButton: true,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						cancelButtonText:'Cancelar',
						confirmButtonText: 'Sí, cambiar estado'
					}).then((result) => {
						if (result.isConfirmed) {
							axios.post('controlador/usuarios.php?op=disabled',{id:id})
								.then(resp =>{
									thisJq.listUsers();
									thisJq.sweetalert2(resp.data.tittle, resp.data.message, resp.data.type);
								}).catch(error => {
									thisJq.sweetalert2('ERROR', 'Error al cambiar estado', 'error');
									console.error(error);
								});
						}
					});
				});
				
				// Botón para editar usuario
				$(document).on("click", "#edit", function() {
					let id = $(this).val();
					axios.post('controlador/usuarios.php?op=getUserById',{id:id})
						.then(resp =>{
							if (resp.data.error) {
								thisJq.sweetalert2('ERROR', resp.data.error, 'error');
							} else {
								thisJq.data.id_usuario = resp.data.id_usuario;
								thisJq.data.login = resp.data.usuario;
								thisJq.data.name = resp.data.nombre;
								thisJq.data.status = resp.data.habilitado;
								thisJq.data.password = ''; // No mostrar la contraseña
								thisJq.modulo = 'Editar Usuario';
								thisJq.list = false;
								thisJq.form = true;
							}
						}).catch(error => {
							thisJq.sweetalert2('ERROR', 'Error al cargar usuario', 'error');
							console.error(error);
						});
				});
				
				// Botón para gestionar permisos
				$(document).on("click", "#permissions", function() {
					let id = $(this).val();
					thisJq.loadPermisos(id);
				});
			});
		},
		
		loadPermisos(id_usuario) {
			axios.post('controlador/usuarios.php?op=getPermisos', {id_usuario: id_usuario})
				.then(resp => {
					// Obtener nombre del usuario
					axios.post('controlador/usuarios.php?op=getUserById', {id: id_usuario})
						.then(userResp => {
							this.permisos.id_usuario = id_usuario;
							this.permisos.nombre_usuario = userResp.data.nombre;
							this.permisos.lista = resp.data;
							this.list = false;
							this.form = false;
							this.permisosView = true;
						});
				}).catch(error => {
					this.sweetalert2('ERROR', 'Error al cargar permisos', 'error');
					console.error(error);
				});
		},
		
		savePermisos() {
			Swal.fire({
				title: '¿Guardar cambios?',
				text: "Se actualizarán los permisos del usuario",
				icon: 'question',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				cancelButtonText:'Cancelar',
				confirmButtonText: 'Sí, guardar'
			}).then((result) => {
				if (result.isConfirmed) {
					axios.post('controlador/usuarios.php?op=savePermisos', {
						id_usuario: this.permisos.id_usuario,
						permisos: this.permisos.lista
					}).then(resp => {
						this.sweetalert2(resp.data.tittle, resp.data.message, resp.data.type);
						if (resp.data.type === 'success') {
							this.cancel();
						}
					}).catch(error => {
						this.sweetalert2('ERROR', 'Error al guardar permisos', 'error');
						console.error(error);
					});
				}
			});
		},
		
		toggleAllPermisos(modulo, tipo) {
			const value = modulo[tipo];
			// Si se activa "ver", activar todas
			if (tipo === 'view' && value) {
				modulo.insert = true;
				modulo.update = true;
				modulo.delete = true;
			}
			// Si se desactiva "ver", desactivar todas
			if (tipo === 'view' && !value) {
				modulo.insert = false;
				modulo.update = false;
				modulo.delete = false;
			}
			// Si se activa cualquier otro, activar "ver"
			if (tipo !== 'view' && value) {
				modulo.view = true;
			}
		},
		
		newbutton(){
			this.resetInput();
			this.modulo = 'Crear Nuevo Usuario';
			this.list = false;
			this.form = true;
		},
		
		resetInput(){
			this.data.id_usuario = '';
			this.data.login = '';
			this.data.password = '';
			this.data.name = '';
			this.data.status = '';
		},
		
		sweetalert2:function(tittle,message,type) {
			Swal.fire(
				'¡'+tittle+'!',
				''+message+'',
				''+type+''
			)
		}
	}
})