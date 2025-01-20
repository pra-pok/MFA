$(() => {
    setMandatoryStar();
});
// function setMandatoryStar() {
//    // console.log("hello world!")
//     $("input[required],select[required],textarea[required]").each(function () {
//         $(this).parent().find("label").append("<span class='mandatory text-danger'>*</span>");
//     });
// }

function setMandatoryStar() {
    // Target elements with the "required" class instead of the "required" attribute
    $("input.required, select.required, textarea.required").each(function () {
        $(this).parent().find("label").append("<span class='mandatory text-danger'>*</span>");
    });
}
