/**
 * open-table.js
 *
 * code for  the open-table.php booking page
 *
 *
 */

jQuery(document).ready(function ($) {
  const rid = tasteBooking.rid;
  const restName = tasteBooking.restName;

  var otLoad = setInterval(function () {
    if ($(".ot-dtp-picker-form .ot-time-picker select").length > 0) {
      clearInterval(otLoad);
      // const $timeSelect = $(".ot-dtp-picker-form .ot-time-picker select");
      const $restSelect = $(".ot-dtp-picker-form .ot-restaurant-picker select");
      const $restSelectLabel = $(".ot-dtp-picker-form .ot-restaurant-picker a");

      $restSelect.html(
        `<option value="${rid}" selected="selected">${restName}</option>`
      );
      $restSelectLabel.html(restName);

      updateOtURL(rid, $);
    }
  }, 100);
});

function updateOtURL(rid, $) {
  let $formSubmit = $(".ot-dtp-picker-form input[type=submit]");
  let submitURL = $formSubmit.data("ot-restref");
  const queryVars = tasteParseURL(submitURL);
  queryVars.rid = rid;
  queryVars.restref = rid;
  const urlStr = OT.Common.Helpers.QueryString.stringify(queryVars);
  $formSubmit.data("ot-restref", urlStr);
  $formSubmit.attr("data-ot-restref", urlStr);
}

function tasteParseURL(url) {
  let queryVars = {}; // create a new empty object

  url.split("&").forEach(function (pair) {
    // iterate over query string parts

    if (pair === "") {
      return;
    } // make sure there is something in it

    let parts = pair.split("="); // setup the seperator to use for splitting

    // populate object
    queryVars[parts[0]] =
      parts[1] && decodeURIComponent(parts[1].replace(/\+/g, " ")); // remove + signs from query parameters just in case
  });

  return queryVars;
}
