(()=>{"use strict";const t=function(t,e){t||(t=0);var n=parseInt(t)/100,i=2;t%100==0&&0==e.decimal_points&&(i=0);var a=",",r=".";"dot_comma"!=e.currency_separator&&(a=".",r=",");var o,s,f,c,m,l,u,p,d,_,h=e.currency_sign||"",y=(o=n,s=i,f=r,c=a,m=isNaN(s)?2:Math.abs(s),l=f||".",u=void 0===c?",":c,p=o<0?"-":"",d=parseInt(o=Math.abs(o).toFixed(m))+"",_=(_=d.length)>3?_%3:0,p+(_?d.substr(0,_)+u:"")+d.substr(_).replace(/(\d{3})(?=\d)/g,"$1"+u)+(m?l+Math.abs(o-d).toFixed(m).slice(2):""));return"right"==e.currency_sign_position?y+""+h:"left_space"==e.currency_sign_position?h+" "+y:"right_space"==e.currency_sign_position?y+" "+h:h+""+y};function e(t,e){for(var n=0;n<e.length;n++){var i=e[n];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(t,i.key,i)}}var n,i=function(){function n(t,e){!function(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}(this,n);var i=e.settings.id;this.$form=t,this.formInstance=e,this.formId=i,this.paymentMethod="",this.paymentConfig=window.fluentform_payment_config,this.appliedCoupons={},this.totalAmount=0,this.formPaymentConfig=window["fluentform_payment_config_"+i]}var i,a,r;return i=n,a=[{key:"$t",value:function(t){return this.paymentConfig.i18n[t]?this.paymentConfig.i18n[t]:t}},{key:"init",value:function(){var t=this;this.initStripeElement(),this.initPaymentMethodChange(),this.$form.on("fluentform_next_action_payment",(function(e,n){var i=n.response.data;i.actionName&&(jQuery("<div/>",{id:"form_success",class:"ff-message-success"}).html(i.message).insertAfter(t.$form),t[i.actionName](i))})),jQuery(".ff_modal_btn").on("click",(function(){t.calculatePayments()})),this.calculatePayments(),this.$form.find(".ff_payment_item,.ff_quantity_item").on("change",(function(e){t.calculatePayments(),t.mayBeToggleSubscriptionRelatedThings(e)})),this.$form.on("change",".ff-custom-user-input",(function(e){t.handleCustomUserInputChange(e)})),this.$form.on("do_calculation",(function(){t.calculatePayments()})),this.initDiscountCode()}},{key:"calculatePayments",value:function(){var t=this,e=this.$form,n=this.getPaymentItems(),i=0;jQuery.each(n,(function(t,e){i+=e.line_total})),this.totalAmount=i;var a=this.getDiscounts();jQuery.each(a,(function(e,n){var a=n.amount;"percent"===n.coupon_type&&(a=n.amount/100*i),t.totalAmount-=a})),e.trigger("payment_amount_change",{amount:i,items:n,discounts:a}),e.find(".ff_order_total").html(this.getFormattedPrice(this.totalAmount)),e.data("payment_total",this.totalAmount);var r=!Object.keys(n).length;this.hasPaymentItems=r;var o=r?"hide":"show";this.$form.find(".ff_payment_method").closest(".ff-el-group:not(.ff_excluded)")[o](),e.find(".ff_dynamic_payment_summary").length&&this.generateSummaryTable(n,i,a,r)}},{key:"generateSummaryTable",value:function(t,e,n){var i=this,a=arguments.length>3&&void 0!==arguments[3]&&arguments[3];if(this.$form.find(".ff_dynamic_payment_summary .ff_payment_summary_fallback").hide(),a)return this.$form.find(".ff_dynamic_payment_summary .ff_payment_summary").html(""),void this.$form.find(".ff_dynamic_payment_summary .ff_payment_summary_fallback").show();var r='<table class="table ffp_table input_items_table">';r+="<thead><tr><th>".concat(this.$t("item"),"</th><th>").concat(this.$t("price"),"</th><th>").concat(this.$t("qty"),"</th><th>").concat(this.$t("line_total"),"</th></tr></thead>"),r+="<tbody>",jQuery.each(t,(function(t,e){(0===e.price||e.price)&&(r+="<tr>",r+="<td>".concat(e.label,"</td>"),r+="<td>".concat(i.getFormattedPrice(e.price),"</td>"),r+="<td>".concat(e.quantity,"</td>"),r+="<td>".concat(i.getFormattedPrice(e.line_total),"</td>"),r+="</tr>")})),r+="</tbody>";var o="";n.length&&(o+='<tr><th class="item_right" colspan="3">'.concat(this.$t("Sub Total"),"</th><th>").concat(this.getFormattedPrice(e),"</th></tr>"),jQuery.each(n,(function(t,n){var a=n.amount;"percent"===n.coupon_type&&(a=n.amount/100*e),a>=e&&(a=e),o+='<tr><th class="item_right" colspan="3">'.concat(i.$t("discount:")," ").concat(n.title,"</th><th>-").concat(i.getFormattedPrice(a),"</th></tr>"),e-=a}))),o+='<tr><th class="item_right" colspan="3">'.concat(this.$t("total"),"</th><th>").concat(this.getFormattedPrice(e),"</th></tr>"),r+="<tfoot>".concat(o,"</tfoot>"),r+="</table>",this.$form.find(".ff_dynamic_payment_summary .ff_payment_summary").html(r)}},{key:"getPaymentItems",value:function(){var t=this.$form,e=t.find(".ff-el-group:not(.ff_excluded)").find(".ff_payment_item"),n={},i=this;function a(t,e,a){t=t.replace("[","").replace("]","");var r=i.getQuantity(t);r&&(n[t]={label:e,price:a,quantity:r,line_total:r*a})}return e.each((function(e,n){var r=n.type,o=jQuery(n);if(!o.closest(".has-conditions.ff_excluded").length){var s=o.attr("name"),f=o.closest(".ff-el-group").find(".ff-el-input--label label").text();if("radio"===r){var c=t.find("input[name="+s+"]:checked"),m=c.parent().find(".ff_plan_title").text();i.maybeHandleSubscriptionItem(s,c,f,m,a)}else if("hidden"===r)i.maybeHandleSubscriptionItem(s,o,f,"",a);else if("number"==r||"text"==r){var l=window.ff_helper.numericVal(jQuery(this));0!=l&&a(s,f,parseFloat(l))}else if("checkbox"==r){var u=o.data("group_id"),p=t.find('input[data-group_id="'+u+'"]:checked'),d=0,_=[];p.each((function(t,e){var n=jQuery(e).data("payment_value");n&&(d+=parseFloat(n),_.push(jQuery(e).parent().find("span").text()))})),_.length&&(f+=' <ul class="ff_sub_items">',_.forEach((function(t){f+="<li>"+t+"</li>"})),f+=" </ul>"),d&&a(s,f,d)}else if("select-one"===r){var h=t.find("select[name="+s+"] option:selected"),y=h.text();i.maybeHandleSubscriptionItem(s,h,f,y,a)}}})),n}},{key:"maybeHandleSubscriptionItem",value:function(t,e,n,i,a){var r=parseFloat(e.attr("data-payment_value")),o=parseFloat(e.attr("data-signup_fee")),s=e.data("trial_days"),f=parseFloat(e.attr("data-initial_amount"));f&&(a(t+"_signup_fee",this.$t("Signup Fee for")+" "+n+(i?" - "+i:""),f),r-=f),(s&&0===r||r)&&(i&&(n+=" - "+i),s&&(n+=" "+this.$t("(Trial)"),r=0),a(t,n,parseFloat(r)),o&&a(t+"_signup_fee",this.$t("Signup Fee for")+" "+n,o))}},{key:"getQuantity",value:function(t){var e=this.$form.find('input[data-target_product="'+t+'"]');if(!e.length)return 1;var n=e.val();return!n||isNaN(n)?0:parseInt(n)}},{key:"getFormattedPrice",value:function(e){return t(parseFloat(100*e).toFixed(2),window["fluentform_payment_config_"+this.formId].currency_settings)}},{key:"stripeRedirectToCheckout",value:function(t){var e=new Stripe(this.formPaymentConfig.stripe.publishable_key);e.registerAppInfo(this.formPaymentConfig.stripe_app_info),e.redirectToCheckout({sessionId:t.sessionId}).then((function(t){console.log(t)}))}},{key:"normalRedirect",value:function(t){window.location.href=t.redirect_url}},{key:"getDiscounts",value:function(){return Object.values(this.appliedCoupons)}},{key:"initDiscountCode",value:function(){var t=this,e=this.$form.find(".ff_coupon_wrapper");if(!e.length)return!1;this.$form.append('<input type="hidden" class="__ff_all_applied_coupons" name="__ff_all_applied_coupons"/>'),jQuery.each(e,(function(e,n){var i=jQuery(n);i.find(".ff_input-group-append").on("click",(function(){var e=i.find("input.ff_coupon_item"),n=e.val();if(!n)return"";e.attr("disabled",!0),e.attr("name"),jQuery.post(window.fluentFormVars.ajaxUrl,{action:"fluentform_apply_coupon",form_id:t.formId,total_amount:t.totalAmount,coupon:n,other_coupons:t.$form.find(".__ff_all_applied_coupons").val()}).then((function(a){var r=a.coupon;t.appliedCoupons[r.code]=r,t.$form.find(".__ff_all_applied_coupons").attr("value",JSON.stringify(Object.keys(t.appliedCoupons)));var o=r.amount+"%";"fixed"==r.coupon_type&&(o=t.getFormattedPrice(r.amount));var s="".concat(r.code," <span>-").concat(o,"</span>");t.recordCouponMessage(i,n,s,"success"),e.val("")})).fail((function(e){t.recordCouponMessage(i,n,e.responseJSON.message,"error")})).always((function(){e.attr("disabled",!1),t.$form.trigger("do_calculation")}))}))}))}},{key:"recordCouponMessage",value:function(t,e,n,i){var a=this;t.find(".ff_coupon_responses").length||t.append('<ul class="ff_coupon_responses"></ul>');var r=t.find(".ff_coupon_responses");r.find(".ff_error, .ff_resp_item_"+e).remove();var o=jQuery("<li/>",{class:"ff_".concat(i," ff_resp_item_").concat(e)}),s=jQuery("<span/>",{class:"error-clear",html:"&times;",click:function(t){r.find(".ff_resp_item_"+e).remove(),delete a.appliedCoupons[e],a.$form.find(".__ff_all_applied_coupons").attr("value",JSON.stringify(Object.keys(a.appliedCoupons))),a.$form.trigger("do_calculation")}});r.append(o.append(s,n))}},{key:"mayBeToggleSubscriptionRelatedThings",value:function(t){var e=jQuery(t.target);if(e.hasClass("ff_subscription_item")){var n=e.val(),i=e.closest(".ff-el-input--content");i.find(".ff-custom-user-input-wrapper").addClass("hidden_field"),i.find(".ff-custom-user-input-wrapper-"+n).removeClass("hidden_field"),i.find(".ff_summary_container").addClass("hidden_field"),i.find(".ff_summary_container_"+n).removeClass("hidden_field")}}},{key:"handleCustomUserInputChange",value:function(t){var e,n=jQuery(t.target),i=parseFloat(n.val())||0,a=n.data("parent_input_name"),r=n.data("parent_input_type"),o=n.data("plan_index");"select"===r?o=(e=this.$form.find("select[name="+a+"] option:selected")).val():e="radio"===r?this.$form.find("input[name="+a+"]:checked"):this.$form.find("input[name="+a+"]");var s=i+parseFloat(e.data("initial_amount"));e.attr("data-payment_value",s);var f=n.parent().parent().find(".ff_summary_container_"+o);f.find(".ffbs_subscription_amount").html(this.getFormattedPrice(i)),f.find(".ffbs_first_interval_total").html(this.getFormattedPrice(s)),this.calculatePayments()}},{key:"initStripeElement",value:function(){var t=this;if(this.$form.hasClass("ff_has_stripe_inline")){this.stripe=new Stripe(this.formPaymentConfig.stripe.publishable_key),this.stripe.registerAppInfo(this.formPaymentConfig.stripe_app_info);var e=this.stripe.elements().create("card",{style:{base:{color:"#32325d",fontFamily:"-apple-system, BlinkMacSystemFont, sans-serif",fontSmoothing:"antialiased",fontSize:"16px","::placeholder":{color:"#aab7c4"}},invalid:{color:"#fa755a",iconColor:"#fa755a"}},hidePostalCode:!this.formPaymentConfig.stripe.inlineConfig.verifyZip}),n=this.$form.find(".ff_stripe_card_element").attr("id");if(!n)return void console.log("No Stripe Cart Element Found");e.mount("#"+n),e.addEventListener("change",(function(e){t.toggleStripeInlineCardError(e.error)})),this.stripeCard=e,this.$form.on("fluentform_submission_success",(function(){e.clear()})),this.$form.on("fluentform_submission_failed",(function(){t.stripeCard.update({disabled:!1})})),this.registerStripePaymentToken(n)}}},{key:"initPaymentMethodChange",value:function(){var t=this,e=this.$form.find(".ff_payment_method");e.length>1?this.paymentMethod=e.filter((function(t,e){return e.checked})).val():this.paymentMethod=e.val(),e.length>1&&e.change((function(e){t.paymentMethod=e.target.value;var n="stripe"===t.paymentMethod?"block":"none";jQuery(e.target).closest(".ff-el-input--content").find(".stripe-inline-wrapper").css({display:n})}))}},{key:"registerStripePaymentToken",value:function(t){var e=this;this.formInstance.addGlobalValidator("stripeInlinePayment",(function(n,i){if("stripe"===e.paymentMethod&&!e.hasPaymentItems&&!jQuery("#"+t).closest(".ff_excluded").length){e.formInstance.showFormSubmissionProgress(n),jQuery("<div/>",{id:e.formId+"_success",class:"ff-message-success ff_msg_temp"}).html(e.$t("processing_text")).insertAfter(e.$form),e.toggleStripeInlineCardError();var a=jQuery.Deferred();return e.stripe.createPaymentMethod("card",e.stripeCard).then((function(t){t.error?e.toggleStripeInlineCardError(t.error):(e.stripeCard.update({disabled:!0}),e.formInstance.hideFormSubmissionProgress(n),jQuery("<div/>",{id:e.formId+"_success",class:"ff-message-success ff_msg_temp"}).html(e.$t("processing_text")).insertAfter(e.$form),i.data+="&"+jQuery.param({__stripe_payment_method_id:t.paymentMethod.id}),a.resolve())})),a.promise()}}))}},{key:"toggleStripeInlineCardError",value:function(t){var e=this,n=this.$form.find(".ff_card-errors");t?(n.html(t.message),n.closest(".stripe-inline-wrapper").addClass("ff-el-is-error"),this.formInstance.hideFormSubmissionProgress(this.$form),this.stripeCard.update({disabled:!1})):(n.html(""),n.closest(".stripe-inline-wrapper").removeClass("ff-el-is-error")),setTimeout((function(){e.maybeRemoveSubmitError()}),500)}},{key:"stripeSetupIntent",value:function(t){var e=this;this.stripe.confirmCardPayment(t.client_secret,{payment_method:t.payment_method_id}).then((function(n){n.error?e.toggleStripeInlineCardError(n.error):e.handleStripePaymentConfirm({action:"fluentform_sca_inline_confirm_payment_setup_intents",form_id:e.formId,payment_method:n.paymentIntent.payment_method,payemnt_method_id:t.payemnt_method_id,payment_intent_id:n.paymentIntent.id,submission_id:t.submission_id,stripe_subscription_id:t.stripe_subscription_id,type:"handleCardSetup"})}))}},{key:"initStripeSCAModal",value:function(t){var e=this;this.formInstance.showFormSubmissionProgress(this.$form),this.stripe.handleCardAction(t.client_secret).then((function(n){n.error?(e.formInstance.hideFormSubmissionProgress(e.$form),e.toggleStripeInlineCardError(n.error)):e.handleStripePaymentConfirm({action:"fluentform_sca_inline_confirm_payment",form_id:e.formId,payment_method:n.paymentIntent.payment_method,payment_intent_id:n.paymentIntent.id,submission_id:t.submission_id,type:"handleCardAction"})}))}},{key:"handleStripePaymentConfirm",value:function(t){this.maybeRemoveSubmitError(),jQuery("<div/>",{id:this.formId+"_success",class:"ff-message-success ff_msg_temp"}).html(this.$t("confirming_text")).insertAfter(this.$form),this.formInstance.showFormSubmissionProgress(this.$form),window.fluentFormApp(this.$form).sendData(this.$form,t)}},{key:"maybeRemoveSubmitError",value:function(){jQuery("#form_success").remove()}}],a&&e(i.prototype,a),r&&e(i,r),Object.defineProperty(i,"prototype",{writable:!1}),n}();(n=jQuery).each(n("form.fluentform_has_payment"),(function(){var t=n(this);t.on("fluentform_init_single",(function(e,n){new i(t,n).init()}))})),n(document).on("ff_reinit",(function(t,e){var a=n(e);a.attr("data-ff_reinit","yes");var r=fluentFormApp(a);if(!r)return!1;new i(a,r).init()}))})();