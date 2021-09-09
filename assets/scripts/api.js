_ = _ || window.lodash
window.dtpsAPICore = dtpsCore
window.dtpsAPI = {

  send_report: ( data ) => makeRequest('POST', 'send_report', { data: data } ),

  community_request: ( data ) => makeRequest('POST', 'community_request', data ),

}
function makeRequest (type, url, data, base = 'dtps/v4/') {
  const options = {
    type: type,
    contentType: 'application/json; charset=utf-8',
    dataType: 'json',
    url: url.startsWith('http') ? url : `${dtpsCore.root}${base}${url}`,
    beforeSend: xhr => {
      xhr.setRequestHeader('X-WP-Nonce', dtpsCore.nonce);
    }
  }

  if (data) {
    options.data = JSON.stringify(data)
  }

  return jQuery.ajax(options)
}
function handleAjaxError (err) {
  if (_.get(err, "statusText") !== "abortPromise" && err.responseText){
    console.trace("error")
    console.log(err)
  }
}
jQuery(document).ajaxComplete((event, xhr, settings) => {
  if (_.get(xhr, 'responseJSON.data.status') === 401) {
    console.trace("error")
    console.log(err)
  }
}).ajaxError((event, xhr) => {
  handleAjaxError(xhr)
})
