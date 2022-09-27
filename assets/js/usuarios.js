new Vue({
	el:'#usuarios',
	data(){
		return{
			data:{
				login:'',
				password:'',
				name:'',
				status:''
			},
			list:true
		}
	},
	created(){

	},
	methods:{
		submit:function() {
			axios.post('controlador/usuarios.php?op=new',this.data)
				.then(resp =>{
					console.log(resp.data);
			})
		},
		newbutton(){
			this.form = false;
			this.resetInput();
		},
		resetInput(){
			this.data.login='';
			this.data.password='';
			this.data.name='';
			this.data.status='';
		}
	}
})
