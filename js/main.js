document.addEventListener('DOMContentLoaded',function(){
    document.querySelectorAll('input[type="file"][data-preview]').forEach(function(el){
        el.addEventListener('change',function(){
            var p=document.getElementById(this.dataset.preview);
            if(p&&this.files&&this.files[0]){p.style.display='block';var r=new FileReader();r.onload=function(e){p.src=e.target.result};r.readAsDataURL(this.files[0])}
        })
    });
    setTimeout(function(){
        document.querySelectorAll('.alert-dismissible').forEach(function(a){
            var bs=bootstrap.Alert.getInstance(a);if(bs)bs.close()
        })
    },5000);
});
function confirmAction(msg,formId){if(confirm(msg||'¿Estás seguro?')){document.getElementById(formId).submit()}}
function formatCurrency(a){return'$'+parseInt(a).toLocaleString('es-CO')}
