$(function () {
  $(document).on("click","#logout",function (e) {
    axios.post('controlador/login.php?op=destroySession').then(resp =>{
      if (resp.data.status == true) {
        window.location = "index.php";
      }else {
        alert(resp.data.status);
      }
    });
    e.preventDefault();
  });
})
