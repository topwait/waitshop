layui.define([], function (exports) {
    let ojb = {
        init: function (options) {
            tinymce.init({
                selector: options.selector || "#textarea",
                language: options.language || "zh_CN",
                menubar: options.menubar || false,
                height: options.height || "600",
                convert_urls: false,
                plugins: `code  preview fullpage searchreplace autolink
                    directionality visualblocks visualchars fullscreen image link media template
                    codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists
                    wordcount imagetools textpattern help emoticons table searchreplace fullscreen autosave
                    autolink insertdatetime pagebreak paste image media preview print visualblocks wordcount codesample`,
                toolbar: [
                    `code
                    | bold italic underline strikethrough forecolor backcolor subscript superscript
                    | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent
                    | emoticons charmap wordcount | fullscreen`,
                    `formatselect
                    | fontselect | fontsizeselect | table hr blockquote codesample link | image media
                    | anchor removeformat print | searchreplace`
                ],
                file_picker_callback: function (callback) {
                    wait.imageUpload({
                        that: $(this),
                        field: "image",
                        limit: 1
                    }).then((res) => {
                        callback(res[0], {alt: "My alt describe"});
                    });
                }
            });
        }
    }

    exports("waitTinymce", ojb);
})