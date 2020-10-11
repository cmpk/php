$(function(){
    //**-- jQuery Validation Plugin --**/
    // 標準エラーメッセージの変更
    $.extend($.validator.messages, {
        required: '入力してください。'
    });

    $('#questionnaire').validate({
        // 検証ルール定義
        rules: {
            shop: {required: true},
            item: {required: true},
            flavour: {required: true}
        },

        // エラーメッセージ出力箇所調整
        errorPlacement: function(error, element) {
            if (element.is(':radio')) {
                error.appendTo(element.parents('.required'));

                // ラジオボタンの場合は、ラジオボタンではなく親要素のスタイルを変更する
                $(element).parents('.radio_group').addClass('error');
            }
            else {
                // 入力フォームの後ろでなく、入力フォームを囲う要素の後ろにエラーメッセージを追加する
                error.appendTo(element.parent().parent());
            }
        }
    });

    //**-- ユーザ操作に伴うアクション --*/
    $('input:radio').each(function(){
        $(this).change(function(){
            $obj = $(this).parents('.radio_group');
            $obj.removeClass('error');
        });
    });
});

$('#questionnaire').submit(function(){
    // 連打禁止
    $('#saver').prop('disabled', true);


    var is_valid = $('#questionnaire').valid();
    if (is_valid) {
        return true;
    }

    $('#beginning_error_message').css('display', 'block');
    $('#saver').prop('disabled', false);
    return false;
});
