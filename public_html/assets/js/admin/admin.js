
$('._datatable').each(function(){
    $(this).footable({
        "paging": {
            "enabled": true,
            "size": 20,
            "limit": 5,
            "position": "center",
            "countFormat": "Page {CP} sur {TP}",
            "container": $(this).prev(".paging-ui-container"),
        },
        "filtering": {
			"enabled": true,
            "dropdownTitle": "Rechercher",
            "min": 2,
            "placeholder": "Rechercher"
		},
        calculateWidthOverride: function() {
        return { width: $(window).width() };
        }
    });
  }); 

/************ "search" item on all selects ******************/
$('select').each(function() {
    let target = $(this).children(':first-child');
    if(target.val() == '') {
        $(this).children(':first-child').html('Sélectionner');
    } 
});
$('.select2').select2();

/********* TinyMCE ************/

var useDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;

tinymce.init({
    selector: 'textarea.tinymce',
    plugins: 'codesnippet searchreplace autolink code visualblocks visualchars fullscreen image link media table charmap anchor advlist lists wordcount help quickbars emoticons',
    menubar: 'file edit view insert tools table help',
    toolbar: 'undo redo | codesnippet | bold italic underline strikethrough | formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | removeformat | charmap emoticons | fullscreen | insertfile image media template link anchor',
    toolbar_sticky: true,
    autosave_ask_before_unload: true,
    autosave_interval: '30s',
    autosave_prefix: '{path}{query}-{id}-',
    autosave_restore_when_empty: false,
    autosave_retention: '2m',
    image_advtab: true,
    height: 600,
    image_caption: true,
    quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
    toolbar_mode: 'sliding',
    contextmenu: 'link image imagetools table',
    skin: useDarkMode ? 'oxide-dark' : 'oxide',
    content_css: useDarkMode ? 'dark' : 'default',
    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
    image_title: true,
    automatic_uploads: true,
    images_upload_url: '/upload_file',
    file_picker_types: 'image',
    file_picker_callback: (cb, value, meta) => {
        const input = document.createElement('input');
        input.setAttribute('type', 'file');
        input.setAttribute('accept', 'image/*');

        input.addEventListener('change', (e) => {
        const file = e.target.files[0];

        const reader = new FileReader();
        reader.addEventListener('load', () => {
            const id = 'blobid' + (new Date()).getTime();
            const blobCache =  tinymce.activeEditor.editorUpload.blobCache;
            const base64 = reader.result.split(',')[1];
            const blobInfo = blobCache.create(id, file, base64);
            blobCache.add(blobInfo);
            cb(blobInfo.blobUri(), { title: file.name });
        });
        reader.readAsDataURL(file);
        });
        input.click();
    },
    codesnippet_languages: [
        { text: 'HTML/XML', value: 'markup' },
        { text: 'JavaScript', value: 'javascript' },
        { text: 'CSS', value: 'css' },
        { text: 'PHP', value: 'php' },
        { text: 'Shell', value: 'shell' },
        { text: 'Swift', value: 'swift' },
        { text: 'Ruby', value: 'ruby' },
        { text: 'Python', value: 'python' },
        { text: 'Java', value: 'java' },
        { text: 'C', value: 'c' },
        { text: 'C#', value: 'csharp' },
        { text: 'C++', value: 'cpp' }
      ],
 });



/********* Delete alert ************/
$(document).on('click', '._deleteBtn', function(e) {
    e.preventDefault();
    let parent = $(this).parent('form');
    $.confirm({
        theme: 'supervan',
        icon: 'fa-solid fa-triangle-exclamation fa-2xl text-red',
        title: '',
        content: 'Supprimer cette donnée ?<br />Attention cette action est irréversible',
        buttons: {
            confirm: {
                text: "Oui",
                action: function() {
                    parent.submit();
                }
            },
            cancel: {
                text: "Non"
            }
        }
    });
});

/********* Close alerts ************/
$(document).ready(function () {
    $( '.alert-close' ).click(function() {
        $( this ).parent().parent().fadeOut();
    });
});

/****** sidebar dropdown *********/
document.addEventListener("DOMContentLoaded", function(){
    document.querySelectorAll('.sidebar .has-icon').forEach(function(element){
        element.addEventListener('click', function (e) {
            document.querySelectorAll('.show').forEach(function(openBlock){
                new bootstrap.Collapse(openBlock);
                let prevEl = openBlock.previousElementSibling;
                prevEl.querySelectorAll('.caretIcon').forEach(function(caret){
                    if(caret.getAttribute('class') == 'caretIcon caretIconOpen'){
                        caret.setAttribute('class', 'caretIcon caretIconClose');
                    } else {
                        caret.setAttribute('class', 'caretIcon caretIconOpen');
                    } 
                });
            });
            let nextEl = element.nextElementSibling;
            let parentEl  = element.parentElement;	
            if(nextEl) {
                e.preventDefault();
                new bootstrap.Collapse(nextEl);
                element.querySelectorAll('.caretIcon').forEach(function(caret){
                    if(caret.getAttribute('class') == 'caretIcon caretIconOpen'){
                        caret.setAttribute('class', 'caretIcon caretIconClose');
                    } else {
                        caret.setAttribute('class', 'caretIcon caretIconOpen');
                    } 
                });
            }
        }); 
    });
});

/*********** suppression logos *************/
$(document).on('click', '._deleteLogo', function(){
    let id = $(this).data('id');
    let entity = $(this).data('entity');
    let current = $(this).parent('div');
    $.confirm({
        theme: 'supervan',
        icon: 'fa-solid fa-triangle-exclamation fa-2xl text-red',
        title: '',
        content: 'Supprimer cette donnée ?<br />Attention cette action est irréversible',
        buttons: {
            confirm: {
                text: "Oui",
                action: function() {
                    $.ajax({
                        url: '/admin/' + entity + '/deleteLogo',
                        type: 'POST',
                        data: 'id=' + id,
                        dataType: false,
                        cache: false
                    })
                    .done(function(res) {
                        if (res.result == 'success') {
                            current.hide();
                        } else {
                            $('#errorIcon').show();
                            $('#alertMessage').html('Une erreur s\'est produite');
                            $('#jsAlertBox').addClass('error');
                            $('#jsAlertBox').show();
                        }
                    });
                }
            },
            cancel: {
                text: "Non"
            }
        }
    });
})

/*********** transactional variables validation ************/
$(document).on('click', '#validVars', function(){
    let tab = new Array;
    $('input[type=checkbox]').each(function(){
        if($(this).prop('checked') == true) {
            let subTab = new Array;
            subTab.push($(this).attr('id'));
            let text = $(this).parent('td').next('td').find('input[type=text]');
            subTab.push(text.val());
            tab.push(subTab);
        }
    })
    $.ajax({
        url: '/admin/transactional/manageVars',
        type: 'POST',
        data: 'tab=' + JSON.stringify(tab),
        dataType: 'json',
        cache: false
    })
    .done(function(res) {
        if (res.result == 'success') {
            $('#successIcon').show();
            $('#alertMessage').html('Mise à jour effectuée');
            $('#jsAlertBox').addClass('success');
            $('#jsAlertBox').show();
        } else {
            $('#errorIcon').show();
            $('#alertMessage').html('Une erreur s\'est produite');
            $('#jsAlertBox').addClass('error');
            $('#jsAlertBox').show();
        }
    });
});

/********** compta ************/

$(document).on('change', '#compta_fk_mvt', function(){
    let id = $(this).val();
    $.ajax({
        url: '/admin/comptamvt/params',
        type: 'POST',
        data: 'id=' + id,
        dataType: 'json',
        cache: false
    })
    .done(function(res) {
        $('#compta_fk_tax option[value=' + res.fk_tax +']').prop('selected', true);
        $('#compta_fk_type').val(res.fk_type);
        let checked = (res.in_ca == 1) ? true : false;
        $('#compta_in_ca').prop('checked', checked);
        let checked2 = (res.urssaf == 1) ? true : false;
        $('#compta_urssaf').prop('checked', checked2);
    });
});

$(document).on('click', '._newComptaEntry', function(e){
    e.preventDefault();
    $.ajax({
        url: '/admin/compta/new',
        type: 'POST',
        data: null,
        dataType: 'json',
        cache: false
    })
    .done(function(res) {
        if (res.result == 'success') {
            $('#modalComptaBody').html(res.html);
            $('#ComptaModalTitle').text('Nouvelle saisie');
            $('.select2').select2('destroy'); 
            $('.select2').select2();
            $('#comtaEntry').modal('show');
        }
    });
});

$(document).on('click', '._updateComptaLine', function(e){
    e.preventDefault();
    let id = $(this).data('id');
    $.ajax({
        url: '/admin/compta/edit',
        type: 'POST',
        data: 'id=' + id,
        dataType: 'json',
        cache: false
    })
    .done(function(res) {
        if (res.result == 'success') {
            $('#modalComptaBody').html(res.html);
            $('#compta_credit').val($('#compta_credit').val().replace('.', ','));
            $('#compta_debit').val($('#compta_debit').val().replace('.', ','));
            $('#ComptaModalTitle').text('Modification saisie');
            $('.select2').select2('destroy'); 
            $('.select2').select2();
            $('#comtaEntry').modal('show');
        }
    });
})

$(document).on('click', '#submitCompta', function(e){
    e.preventDefault();
    $.ajax({
        url: '/admin/compta/persist',
        type: 'POST',
        data: $('form[name=compta]').serialize(),
        processData: false,
        dataType: false,
        cache: false
    })
    .done(function(res) {
        window.location.reload();
    });
});

$(document).on('click', '._update_state', function(e){
    e.preventDefault();
    let id = $(this).data('id');
    let field = $(this).data('field');
    let status = $(this).attr('data-state');
    let parent = $(this);
    let target = $(this).children('i');
    $.ajax({
        url: '/admin/compta/update_state',
        type: 'POST',
        data: 'id=' + id + '&field=' + field + '&status=' + status,
        dataType: 'json',
        cache: false
    })
    .done(function() {
        if(status == 1) {
            target.removeClass('text-red').addClass('text-green');
            parent.attr('data-state', 0);
        } else {
            target.removeClass('text-green').addClass('text-red');
            parent.attr('data-state', 1);
        }
    });
});

$(document).on('change', '#projectStatus', function(){
    let status = $(this).val();
    let id = $(this).data('id');
    $.ajax({
        url: '/admin/projects/update_state',
        type: 'POST',
        data: 'id=' + id  + '&status=' + status,
        dataType: 'json',
        cache: false
    })
    .done(function(res) {
        if (res.result == 'success') {
            $('#successIcon').show();
            $('#alertMessage').html('Mise à jour effectuée');
            $('#jsAlertBox').addClass('success');
            $('#jsAlertBox').show();
        } else {
            $('#errorIcon').show();
            $('#alertMessage').html('Une erreur s\'est produite');
            $('#jsAlertBox').addClass('error');
            $('#jsAlertBox').show();
        }
    });
});

$(document).on('mouseover', 'span.form-control', function(){
    $(this).children('i').removeClass('hidden');
});
$(document).on('mouseout', 'span.form-control', function(){
    $(this).children('i').addClass('hidden');
});

$(document).on('click', '._generateProjectPdf', function(e){
    e.preventDefault();
    let id = $(this).data('id');
    let type = $(this).data('type');
    let advance = $('#advanceAmount').val();
    let refund = $('#refundAmount').val();
    let desc = $('#refundDescription').val();
    let invoice = $('#refundInvoice').val();
    $.ajax({
        url: '/admin/projects/generate_project_pdf',
        type: 'POST',
        data: 'id=' + id + '&type=' + type + '&advance=' + advance + '&refund=' + refund + '&desc=' + desc + '&invoice=' + invoice,
        dataType: 'json',
        cache: false
    })
    .done(function(res) {
        if (res.result == 'success') {
            $('#advanceModal').modal('hide');
            $('#successIcon').show();
            $('#alertMessage').html('Document généré');
            $('#jsAlertBox').addClass('success');
            $('#jsAlertBox').show();
            if(res.type == 'invoice') {
                $('#invoicesBlock').show();
                let html = '<tr><td>' + res.date + '</td><td>' + res.num + '</td><td>' + res.amount + ' €</td><td>' + res.vat + ' €</td><td>' + res.total + ' €</td>';
                html += '<td><a href="/assets/pdf/invoices/' + res.file + '" target="_blank" class="btn btn-info">Télécharger la facture</a></tr>';
                $('#invoiceTable > tbody').append(html);
                $('#invoiceBtn').hide();
            }
        } else {
            $('#errorIcon').show();
            $('#alertMessage').html('Impossible de générer le document !');
            $('#jsAlertBox').addClass('error');
            $('#jsAlertBox').show();
        }
    });
});

$(document).on('click', '._openAdvanceInvoice', function(e){
    e.preventDefault();
    let id = $(this).data('id');
    $.ajax({
        url: '/admin/projects/get_project_rest',
        type: 'POST',
        data: 'id=' + id,
        dataType: 'json',
        cache: false
    })
    .done(function(res) {
        if (res.result == 'success') {
            $('#restText').html(res.amount);
            $('#advanceModal').modal('show');
        } else {
            $('#errorIcon').show();
            $('#alertMessage').html('Une erreur s\'est produite !');
            $('#jsAlertBox').addClass('error');
            $('#jsAlertBox').show();
        }
    });
});

$(document).on('click', '._openRefundInvoice', function(e){
    e.preventDefault();
    let id = $(this).data('id');
    $.ajax({
        url: '/admin/projects/get_project_refund',
        type: 'POST',
        data: 'id=' + id,
        dataType: 'json',
        cache: false
    })
    .done(function(res) {
        if (res.result == 'success') {
            $('#refundText').html(res.amount);
            $('#refundModal').modal('show');
        } else {
            $('#errorIcon').show();
            $('#alertMessage').html('Une erreur s\'est produite !');
            $('#jsAlertBox').addClass('error');
            $('#jsAlertBox').show();
        }
    });   
});

$(document).on('click', '._updateInvoice', function(e){
    e.preventDefault();
    let id = $(this).data('id');
    $.ajax({
        url: '/admin/invoices/get_invoice_to_validate',
        type: 'POST',
        data: 'id=' + id,
        dataType: 'json',
        cache: false
    })
    .done(function(res) {
        if (res.status == 'success') {
            $('#homeInvoiceModalContent').html(res.html)
            $('#homeInvoiceModal').modal('show');
        } else {
            $('#errorIcon').show();
            $('#alertMessage').html('Une erreur s\'est produite !');
            $('#jsAlertBox').addClass('error');
            $('#jsAlertBox').show();
        }
    });   
});

$(document).on('change', '#invoiceStatusSelect', function(){
    let id = $(this).val();
    $('._collapsibleFormGroup').hide();
    if(id == 2) {
        $('#advanceBlock').show();
    } else if(id == 4) {
        $('#checkBlock').show();
    }
});

$(document).on('change', '#projectType', function(e){
    e.preventDefault();
    let project = $(this).data('id');
    let id = $(this).val();
    $.ajax({
        url: '/admin/projects/update_type',
        type: 'POST',
        data: 'id=' + id + '&project=' + project,
        dataType: 'json',
        cache: false
    })
    .done(function(res) {
        if (res.status == 'success') {
            $('#successIcon').show();
            $('#alertMessage').html('Modification effectuée');
            $('#jsAlertBox').addClass('success');
            $('#jsAlertBox').show();
        } else {
            $('#errorIcon').show();
            $('#alertMessage').html('Une erreur s\'est produite !');
            $('#jsAlertBox').addClass('error');
            $('#jsAlertBox').show();
        }
    });  
})

$('body').on('click', '._activeButton', function (e) {
    let id = $(this).data('id');
    let entity = $(this).data('entity');
    let status = $(this).data('status');
    let cible = $(this).children('i');
    $.confirm({
        theme: 'supervan',
        icon: 'fa-solid fa-triangle-exclamation fa-2xl text-red',
        title: '',
        content: status == 1 ? 'Mettre en ligne ?' : 'Désactiver ?',
        buttons: {
            confirm: {
                text: "Oui",
                action: function() {
                    $.ajax({
                        url: '/admin/' + entity + '/activate',
                        type: 'POST',
                        data: 'id=' + id + '&status=' + status,
                        dataType: false,
                        cache: false
                    })
                    .done(function(s) {
                        $('#jsAlertBox').removeClass('success');
                        $('#jsAlertBox').removeClass('error');
                        $('#jsAlertBox').removeClass('notice');
                        $('#jsAlertBox').removeClass('wait');
                        var res = jQuery.parseJSON(s);
                        if (res.result == 'success') {
                            $('#successIcon').show();
                            $('#alertMessage').html('Activation effectuée');
                            $('#jsAlertBox').addClass('success');
                            cible.toggleClass('fa-circle-check fa-circle-xmark').toggleClass('text-green text-red');
                        } else {
                            $('#errorIcon').show();
                            $('#alertMessage').html('Une erreur s\'est produite');
                            $('#jsAlertBox').addClass('error');
                        }
                        $('#jsAlertBox').show();
                    });
                }
            },
            cancel: {
                text: "Non"
            }
        }
    });
});

$('._getFormElementId').each(function(){
    let id = $(this).children().children().first().next().attr('id');
    $(this).attr('id', 'col-' + id);
});

function inArray(needle, haystack){
    var found = 0;
    for (var i=0, len=haystack.length;i<len;i++) {
        if (haystack[i] == needle) return i;
            found++;ê
    }
    return -1;
}

$(document).on('change', '#external_fk_type', function(){
    $('._getFormElementId').hide();
    let id = $(this).val();
    $.ajax({
        url: '/admin/external_types/get_type',
        type: 'POST',
        data: 'id=' + id,
        dataType: 'json',
        cache: false
    })
    .done(function(res) {
        if (res.status == 'success') {console.log(res.fields)
            $('._getFormElementId').each(function(){
                let id = $(this).attr('id');
                let value = id.replace('col-external_', '');
                if(inArray(value, res['fields']) != -1) {
                    $(this).show();
                }
            });
        } else {
            $('#errorIcon').show();
            $('#alertMessage').html('Une erreur s\'est produite !');
            $('#jsAlertBox').addClass('error');
            $('#jsAlertBox').show();
        }
    });  
})