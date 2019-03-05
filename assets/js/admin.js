(function($){
   $(function(){
    $("#buscarEmails").on('click', function(){

        let callAjax = $.ajax({
            url : wems_ex.ajax_url,
            type: 'get',
            data: {'action' : 'buscaClientes'}

        });

        callAjax.done(function(resp){
            console.log(resp);
        });
   });
   });
  
   
})(jQuery);