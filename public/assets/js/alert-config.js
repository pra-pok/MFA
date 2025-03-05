const SUCCESS_ALERT = "SUCCESS_ALERT";
const ERROR_ALERT = "ERROR_ALERT";
const INFO_ALERT = "INFO_ALERT";
const WARNING_ALERT = "WARNING_ALERT";

function showAlert(content, isSuccessAlert = true, title = null, callback) {
    if (!content) return;
    let type = "blue", icon = "fas fa-bullhorn";
    if (!title) {
        // console.log()
        if (typeof isSuccessAlert === 'boolean') {
            title = isSuccessAlert ? "Success" : "Failed";
            type = isSuccessAlert ? "green" : "red";
            icon = isSuccessAlert ? "fa fa-check" : "fa fa-times";
        } else {
            switch (isSuccessAlert) {
                case SUCCESS_ALERT:
                    title = "Success";
                    type = "green";
                    icon = "fa fa-check";
                    break;

                case ERROR_ALERT:
                    title = "Failed";
                    type = "error";
                    icon = "fa fa-times";
                    break;

                case INFO_ALERT:
                    title = "Message";
                    type = "blue";
                    icon = "fa fa-info-circle";
                    break;

                case WARNING_ALERT:
                    title = "Warning";
                    type = "orange";
                    icon = "fa fa-warning";
                    break;

                default:
                    break;
            }
        }
    }

    $.alert({
        title: title,
        type: type,
        icon: icon,
        content: content,
        draggable: false,
        onOpen: function () {
            $(this.$btnc[0]).find(".btn:eq(0)").focus();
        },
        buttons: {
            ok: {
                action: function () {
                    if (callback) {
                        if (callback.func) {
                            setTimeout(function () {
                                let params = callback.params;
                                callback.func.apply(null, params);
                            }, 500);
                        } else callback();
                    }
                }
            }
        }
        // useBootstrap: false
    });
}

const defaultConfirmButtons = [{text: 'Yes [y]'}, {text: 'No [n]'}];

function triggerLoaderCountdown(instance, config) {
    let max = config.timer ?? 5;
    const buttonText = config?.text ?? defaultConfirmButtons[0].text;
    showButtonLoader(instance, buttonText + ` (${max})`, {includeDots: false, includeLoader: false});

    let countdownInterval = setInterval(function () {
        max = max - 1;
        instance.find(".loader-text").html(buttonText + ` (${max})`);

        if (max <= 0) {
            clearInterval(countdownInterval);
            hideButtonLoader(instance);
        }
    }, 2000);
}

function showConfirmDialog(title, content, positiveCallback, negativeCallback, actionButtons = defaultConfirmButtons) {
    showConfirmDialogV2(title, content, {positiveCallback, negativeCallback, actionButtons, countdown: false});
}

function showConfirmDialogV2(title, content, config = {actionButtons: defaultConfirmButtons}) {
    const actionButtons = typeof config.actionButtons !== "undefined" ? config.actionButtons : defaultConfirmButtons;

    $.confirm({
        title: title,
        content: content,
        draggable: false,
        type: "orange",
        icon: 'fa fa-warning',
        columnClass: 'col-md-5',
        onOpen: function () {
            $(this.$btnc[0]).find(".btn:eq(0)").focus();
            if (config?.countdown)
                triggerLoaderCountdown($(this.$btnc[0]).find(".btn:eq(0)"), config);
        },
        buttons: {
            yes: {
                keys: ['y'],
                text: actionButtons[0].text,
                btnClass: 'btn-primary',
                action: function () {
                    if (config.positiveCallback) {
                        const instance = $(this.$btnc[0]).find(".btn-primary");
                        showButtonLoader(instance, PLEASE_WAIT);//no need to call hideButtonLoader because each call to this method generates new dialog with initial state
                        let params = config.positiveCallback.params;
                        config.positiveCallback.func.apply(null, params);
                    }
                }
            },
            no: {
                keys: ['n'],
                text: actionButtons[1].text,
                action: function () {
                    if (config.negativeCallback) {
                        let params = config.negativeCallback.params;
                        config.negativeCallback.func.apply(null, params);
                    }
                }
            },
        }
    });
}

function confirmAndDelete(callback, params) {
    showConfirmDialog(CONFIRM_DELETE, DELETE_CONFIRMATION, generateCallback(callback, params));
}

function confirmAndSubmit(instance, validateMethod = null, saveAndPrint = false, action = null) {
    showConfirmDialog("Confirm Action", `Are you sure you want to ${action ? action : 'perform this action'} ?`, generateCallback(submitFormNow, [instance, validateMethod, saveAndPrint]));
}

function confirmAndEval(instance) {
    showConfirmDialog("Confirm Action", "Are you sure you want to perform this action ?", generateCallback(evalMethod, [instance]));
}

function evalMethod(instance) {
    let that = instance;
    let onValidMethod = instance.attr('onValid');
    onValidMethod = onValidMethod.replace('this', 'that');
    eval(onValidMethod);
}
function submitFormNow(buttonInstance, validateMethod = null, saveAndPrint = false) {
    let formInstance = $(buttonInstance).closest("form");
    /*if (typeof (validateMethod) !== 'boolean') {
        if (validateMethod && !validateMethod()) return;
        if (!isValidForm(formInstance)) return;
    }*/
    if (saveAndPrint) $("input[name=saveAndPrint]").val("1");
    formInstance.submit();
    showButtonLoader(buttonInstance, PLEASE_WAIT);
}
/**
 *
 * @param func
 * @param params
 * @returns {{func: *, params: *}}
 */
function generateCallback(func, params) {
    return {func, params};
}
function confirmAndAssign(callback, params) {
    showConfirmDialog(CONFIRM_MAKE_SLIDER, SLIDER_CONFIRMATION, generateCallback(callback, params));
}
function executeCallback(callback) {
    if (!callback) return;
    setTimeout(function () {
        let params = callback.params;
        callback.func.apply(null, params);
    }, 100);
}
