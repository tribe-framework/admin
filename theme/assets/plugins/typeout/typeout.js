function initTypeOut () {
    document.execCommand('enableObjectResizing');
    document.execCommand('enableInlineTableEditing');

    $('.typeout-exec').on("click", function() {
        document.execCommand($(this).data('typeout-command'));
    });

    $('.typeout-input-exec').on("click", function() {
        var savedSel = saveSelection();
        restoreSelection(savedSel);
        document.execCommand($(this).data('typeout-command'), '', $(this).data('typeout-info'));
    });

    $('.typeout-fullscreen').on("click", function() {
        if ($(this).data('expanded')=='0') {
            $('.input-group').hide(500);
            $('.form-group').hide(500);
            $('.form_title').hide(500);
            $('.btn-toolbar').animate({'opacity': '0.7'}, 'slow');
            $('.navbar').hide(500);
            $(this).data('expanded', '1');
            $('.typeout-content').css('width', '100%').css('height', '100vw');
        }
        else {
            $('.input-group').show(500);
            $('.form-group').show(500);
            $('.form_title').show(500);
            $('.btn-toolbar').css('opacity', '');
            $('.navbar').show(500);
            $(this).data('expanded', '0');
            $('.typeout-content').css('width', '').css('height', '');
        }
    });

    $('.typeout-input').on("click", function() {
        var savedSel = saveSelection();
        var inputData = prompt($(this).data('typeout-info'), '');
        restoreSelection(savedSel);
        if ($(this).data('typeout-command')=='insertPDF') {
            if(inputData)
                inputData='<iframe border="0" width="100%" height="600px" src="https://drive.google.com/viewer?embedded=true&url='+inputData+'"></iframe>';
            command='insertHTML';
        }
        else
            command=$(this).data('typeout-command');
        if (inputData)
            document.execCommand(command, false, inputData);
    });

    $(".typeout-content").focusout(function(){
        var element = $(this);
        if (!element.text().replace(" ", "").length) {
            element.empty();
        }
    });
}

$(function () {
  $('[data-toggle="tooltip"]').tooltip();
})

function restoreSelection (savedSel) {
    if (savedSel) {
        if (window.getSelection) {
            sel = window.getSelection();
            sel.removeAllRanges();
            for (var i = 0, len = savedSel.length; i < len; ++i) {
                sel.addRange(savedSel[i]);
            }
        } else if (document.selection && savedSel.select) {
            savedSel.select();
        }
    }
}

function saveSelection () {
	sel = window.getSelection();
	if (sel && sel.getRangeAt && sel.rangeCount) {
        var ranges = [];
        for (var i = 0, len = sel.rangeCount; i < len; ++i) {
            ranges.push(sel.getRangeAt(i));
        }
        return ranges;
    }
    else if (document.selection && document.selection.createRange) {
        return document.selection.createRange();
    }
    else {
	    return 0;
    }
}