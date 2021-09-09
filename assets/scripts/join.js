_ = _ || window.lodash
const { __, _x, _n, _nx } = wp.i18n;
if ( dtpsAPICore === 'undefined') {
  let dtpsAPICore = window.dtpsAPICore
}
if ( dtpsAPI === 'undefined' ) {
  let dtpsAPI = window.dtpsAPI
}

jQuery(document).ready(function(){
  let button = jQuery('.join-the-community')

  button.on('click', function(){

    jQuery('#modal-content').empty().html(`
    <div class="grid-y padding-1 blog">
        <div class="cell">
            <h1 class="center secondary-color">Join Us</h1>
            <h3 class="center">Zúme is a community of practice for those who want to see disciple making movements.</h3>
        </div>
        <div class="cell"><hr></div>

        <div class="cell ">
            <div class="grid-x grid-padding-x">
                <div class="cell medium-4">
                    <h3>Membership is ...</h3>
                    <ul>
                        <li>Free</li>
                        <li>Non-exclusive</li>
                    </ul>
                    <h3>What is required?</h3>
                    <ul>
                        <li>Openness to working collaboratively</li>
                        <li>Commitment to CPM/DMM principles and practice</li>
                    </ul>
                    <h3>What does this get me?</h3>
                    <ul>
                        <li>Connection to local practitioners</li>
                        <li>Connection to coaching</li>
                        <li>Connection to local peer mentoring groups</li>
                        <li>Connection to local, regional, and global gatherings</li>
                        <li>Vision casting tools</li>
                        <li>Disciple making movement software</li>
                    </ul>

                </div>
                <div class="cell medium-8">
                    <form id="connection-request-form" data-abide>
                      <div data-abide-error class="alert callout" style="display: none;">
                          <p><i class="fi-alert"></i> There are some errors in your form.</p>
                      </div>
                      <table class="unstriped">
                          <tr style="vertical-align: top;">
                              <td style="width:150px;">
                                  <label for="dtps_full_name">Name</label>
                              </td>
                              <td>
                                  <input type="text"
                                         placeholder="${__('First and last name', 'dtps')}"
                                         aria-describedby="${__('First and last name', 'dtps')}"
                                         class="profile-input"
                                         id="dtps_full_name"
                                         name="dtps_full_name"
                                         value=""
                                         required />
                              </td>
                          </tr>
                          <tr>
                              <td style="vertical-align: top;">
                                  <label for="dtps_phone_number">${__('Phone Number', 'dtps')}</label>
                              </td>
                              <td>
                                  <input type="tel"
                                         placeholder="111-111-1111"
                                         class="profile-input"
                                         id="dtps_phone_number"
                                         name="dtps_phone_number"
                                         value=""
                                         required
                                  />
                              </td>
                          </tr>
                          <tr>
                              <td style="vertical-align: top;">
                                  <label>${__('How should we contact you?', 'dtps')}</label>
                              </td>
                              <td>
                                  <fieldset>
                                      <input id="dtps_contact_preference3" name="dtps_contact_preference" class="dtps_contact_preference" type="radio" value="phone" checked data-abide-ignore>
                                      <label for="dtps_contact_preference3">${__('Phone', 'dtps')}</label>
                                      <input id="dtps_contact_preference2" name="dtps_contact_preference" class="dtps_contact_preference" type="radio" value="text" data-abide-ignore>
                                      <label for="dtps_contact_preference2">${__('SMS/Text', 'dtps')}</label>
                                      <input id="dtps_contact_preference4" name="dtps_contact_preference" class="dtps_contact_preference" type="radio" value="whatsapp" data-abide-ignore>
                                      <label for="dtps_contact_preference4">${__('WhatsApp', 'dtps')}</label>
                                      <input id="dtps_contact_preference1" name="dtps_contact_preference" class="dtps_contact_preference" type="radio" value="email" data-abide-ignore>
                                      <label for="dtps_contact_preference1">${__('Email', 'dtps')}</label>
                                  </fieldset>
                              </td>
                          </tr>
                          <tr>
                              <td style="vertical-align: top;">
                                  <label for="user_email">${__('Email', 'dtps')}</label>
                              </td>
                              <td>
                                  <input type="email"
                                         class="profile-input"
                                         style="display:none;"
                                         placeholder="name@email.com"
                                         id="email"
                                         name="email"
                                         value=""
                                  />
                                  <input type="text"
                                         class="profile-input"
                                         placeholder="name@email.com"
                                         id="user_email"
                                         name="user_email"
                                         value=""
                                         required
                                  />
                                  <span class="form-error">
                                     ${__('Email is required.', 'dtps')}
                                  </span>
                              </td>
                          </tr>

                          <tr>
                              <td style="vertical-align: top;">
                                  <label for="validate_address">
                                      ${__('City', 'dtps')}
                                  </label>
                              </td>
                              <td>
                                <div class="input-group">
                                    <input type="text"
                                           placeholder="${__('What is your city or state or postal code?', 'dtps')}"
                                           class="profile-input input-group-field"
                                           id="validate_address"
                                           name="validate_address"
                                           value=""
                                           onkeyup="validate_timer(jQuery(this).val())"
                                           required
                                    />
                                    <div class="input-group-button">
                                        <button class="button hollow" id="spinner_button" style="display:none;"><img src="${dtpsCore.theme_uri}/assets/images/spinner.svg" alt="spinner" style="width: 18px;" /></button>
                                    </div>
                                </div>

                                <div id="possible-results">
                                    <input type="radio" style="display:none;" name="dtps_user_address" id="dtps_user_address" value="current" checked/>
                                </div>
                            </td>
                          </tr>
                          <tr>
                            <td></td>
                            <td><div class="g-recaptcha" id="g-recaptcha"></div></td>
                          </tr>
                          <tr>
                            <td></td>
                            <td><p>${__('On submitting this request, we will do our best to connect you with a community near you.', 'dtps')}</p></td>
                          </tr>
                          <tr>
                            <td></td>
                            <td>
                                <div data-abide-error  class="alert alert-box" style="display:none;" id="alert">
                                    <strong>${__('Oh snap!', 'dtps')}</strong>
                                </div>
                                <button class="button membership-request-form" type="button" onclick="load_form_validator()" id="submit" disabled>${__('Submit', 'dtps')}</button> <span id="request_spinner"></span>
                            </td>
                          </tr>
                      </table>

                      <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
                  </form>
                </div>
            </div>
        </div>
    </div>`)

    new Foundation.Abide( jQuery('#connection-request-form') )

    let validate_address_textbox = jQuery('#validate_address')

    validate_address_textbox.on('keypress', function (e) {
      if (e.which === 13) {
        validate_user_address_v4(validate_address_textbox.val())
        clear_timer()
      }
    });

    jQuery(document)
      .on("formvalid.zf.abide", function (ev, frm) {
        jQuery('#submit').prop('disabled', 'true')
        send_community_request()
      })

    jQuery('#join-modal').addClass('large').foundation('open')

  }) /* end onclick listener */
})

// delay location lookup
window.validate_timer_id = '';
function validate_timer(user_address) {
  // clear previous timer
  clear_timer()

  // toggle buttons
  jQuery('#validate_address_button').hide()
  jQuery('#spinner_button').show()

  // set timer
  window.validate_timer_id = setTimeout(function(){
    // call geocoder
    validate_user_address_v4(user_address)
    // toggle buttons back
    jQuery('#validate_address_button').show()
    jQuery('#spinner_button').hide()
  }, 1500);
}
function clear_timer() {
  clearTimeout(window.validate_timer_id);
}
// end delay location lookup

function validate_user_address_v4(user_address){

  if ( user_address.length < 1 ) {
    return;
  }

  let root = 'https://api.mapbox.com/geocoding/v5/mapbox.places/'
  let settings = '.json?types=country,region,postcode,district,place,locality,neighborhood,address&limit=6&access_token='
  let key = dtpsJoin.map_key

  let url = root + encodeURI( user_address ) + settings + key

  jQuery.get( url, function( data ) {

    let possible_results = jQuery('#possible-results')
    possible_results.empty().append(`<fieldset id="multiple-results"></fieldset>`)
    let multiple_results = jQuery('#multiple-results')

    if( data.features.length < 1 ) {
      multiple_results.empty().append(`${__( 'No location matches found. Try a less specific address.', 'dtps' )}`)
    }

    // Set globals
    // console.log(data)
    window.location_grid_meta = false
    window.mapbox_results = data

    // loop results
    jQuery.each( data.features, function( index, value ) {
      let checked = ''
      if( index === 0 ) {
        checked = 'checked'
      }
      multiple_results.append( `<input type="radio" name="dtps_user_address" id="dtps_user_address${_.escape( index )}" value="${_.escape( value.id )}" ${_.escape( checked )} /><label for="dtps_user_address${_.escape( index )}">${_.escape( value.place_name )}</label><br>`)
    })

    // add responsive click event to populate text area, if selection is clicked. Expected user feedback.
    jQuery('#multiple-results input').on('click', function( ) {
      let selected_id = jQuery(this).val()
      jQuery.each( window.mapbox_results.features, function(i,v) {
        if ( v.id === selected_id ) {
          jQuery('#validate_address').val(_.escape( v.place_name ))
        }
      })
    })

  }); // end get request
} // end validate_user_address

function load_form_validator() {
  jQuery('#connection-request-form').foundation('validateForm');
}

function send_community_request() {
  let honey = jQuery('#email').val()
  let spinner = jQuery('#request_spinner')
  if ( honey !== '' ) {
    spinner.html('buzz, buzz, buzz. I caught a bee.')
    return;
  }

  spinner.html( `<img src="${dtpsCore.theme_uri}/assets/images/spinner.svg" style="width: 40px; vertical-align:top; margin-left: 5px;" alt="spinner" />` )

  let name = jQuery('#dtps_full_name').val()
  let phone = jQuery('#dtps_phone_number').val()
  let email = jQuery('#user_email').val()
  let preference = jQuery('input.dtps_contact_preference:checked').val()

  /**************/
  // Get address
  let location_grid_meta = false // base is false
  let selection_id = jQuery('#possible-results input:checked').val()

  // check if location grid
  if ( window.location_grid_meta && selection_id === 'current' ) {
    location_grid_meta = window.location_grid_meta
  }
  // check if mapbox results
  else if ( window.mapbox_results ) {
    // loop through features
    jQuery.each( window.mapbox_results.features, function(i,v) {
      if ( v.id === selection_id ) {
        location_grid_meta = {
          lng: v.center[0],
          lat: v.center[1],
          level: v.place_type[0],
          label: v.place_name,
          source: 'user',
          grid_id: false
        }
      }
    })
  }
  /**************/

  let data = {
    "name": name,
    "phone": phone,
    "preference": preference,
    "email": email,
    "location_grid_meta": location_grid_meta,
  }

  dtpsAPI.community_request( data ).done( function(data) {
    console.log('postsend')
    console.log(data)
    jQuery('#connection-request-form').html('Excellent! Our volunteer network is being alerted. Please, be watching for a call or email in the next few days.')
  })
    .fail(function(e){
      console.log('coach_request error')
      console.log(e)
      spinner.empty().html( `${__('Oops. Something went wrong. Try again!', 'dtps')}`)
    })
}
