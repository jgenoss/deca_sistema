new Vue({
  el:'#login',
  data(){
    return {
      auth:{
        user:'',
        pass:''
      }
    }
  },
  created(){
    //
  },
  methods:{
    submitLogin:function () {
      axios.post('controlador/login.php?op=setLogin', this.auth).then(resp => {
        if (resp.data.injection == true) {
          this.sweetalert2("ALERTA","Inyección SQL detectada: asegúrese de usar solo letras y números","error");
        }else if (resp.data.type == "success") {
          this.SuccessLogin(resp.data.message,resp.data.type);
          setInterval(function() {
            window.location = "home.php?page=dashboard";
          },3000);
        }else {
          this.sweetalert2(resp.data.tittle,resp.data.message,resp.data.type);
        }
      });
    },
    sweetalert2:function(tittle,message,type) {
			Swal.fire(
				'¡'+tittle+'!',
				''+message+'',
				''+type+''
			)
		},
    SuccessLogin:function (message,type) {
      const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer)
          toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
      })
      Toast.fire({
        icon: type,
        title: message
      })
    }
  }
})
