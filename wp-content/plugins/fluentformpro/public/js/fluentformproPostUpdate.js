!function(e){var t=window.fluentformpro_post_update_vars.post_selector;function n(t,n){var a="Something is wrong when doing ajax request! Please try again";t.responseJSON&&t.responseJSON.data&&t.responseJSON.data.message?a=t.responseJSON.data.message:t.responseJSON&&t.responseJSON.message?a=t.responseJSON.message:t.responseText&&(a=t.responseText);var o=e("<div/>",{class:"error text-danger"});n.closest(".ff-el-group").addClass("ff-el-is-error"),n.closest(".ff-el-input--content").find("div.error").remove(),n.closest(".ff-el-input--content").append(o.text(a))}e(document).on("change","#"+t,(function(t){var a=this,o=e(this).val();o&&jQuery.post(window.fluentFormVars.ajaxUrl,{action:"fluentformpro_get_post_details",post_id:o,fluentformpro_post_update_nonce:window.fluentformpro_post_update_vars.nonce}).then((function(t){var o,s,r;t.data.post?(o=t.data.post,s=e(a),r=s.closest("form"),e.each(o,(function(t,n){if("post_content"==t)e(r).find("input[name='"+t+"']").attr("id"),window.wpActiveEditor&&tinyMCE.get(wpActiveEditor).setContent(n);else if("thumbnail"==t){var a=e(r).find("input[name='featured_image']");if(a.length&&n){a.closest(".ff-el-input--content").find(".ff-post-update-thumb").length&&a.closest(".ff-el-input--content").find(".ff-post-update-thumb").remove();var o='<img class="ff-post-update-thumb" src="'+n+'" >';a.closest(".ff-el-input--content").append(o)}}else"post_excerpt"==t?e(r).find("textarea[name='"+t+"']").val(n).trigger("change"):e(r).find("input[name='"+t+"']").val(n).trigger("change")})),function(t,n){var a=n.closest("form");e.each(t,(function(t,n){var o=e(a).find("[data-name="+t+"]");if("select"==o.attr("type"))o.hasClass("ff_has_multi_select")?o.data("choicesjs")&&(o.data("choicesjs").removeActiveItems(),o.data("choicesjs").setValue(n)):n.length&&o.val(n[0].value);else if("checkbox"==o.attr("type")){var s=n.map((function(e){return e.value.toString()}));o.each((function(t,n){-1!=e.inArray(e(n).val(),s)?e(n).prop("checked",!0):e(n).prop("checked",!1)}))}else{var r=n.map((function(e){return e.label})).join(",");o.val(r)}o.change()}))}(t.data.taxonomy,e(a))):n(t,e(a))})).fail((function(t){n(t,e(a))})).always((function(){}))})),setTimeout((function(){e("#"+t).change()}),500)}(jQuery);