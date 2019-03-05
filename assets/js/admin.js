(function($){
   $(function(){
    $("#buscarEmails").on('click', function(){

        let callAjax = $.ajax({
            url : wems_ex.ajax_url,
            type: 'post',
            data: {'action' : 'buscaClientes'}

        });

        callAjax.done(function(resp){
           let result = JSON.parse(resp);
            $("#quantidade_emails").text(result.count);
        });
   });
   });
  
   
})(jQuery);