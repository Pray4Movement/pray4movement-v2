_ = _ || window.lodash

jQuery(document).ready(function(){
  let chart = jQuery('#chart')

  chart.html(`
    <div class="grid-x white-section">
        <div class="cell center"><h2>Statistics</h2></div>
        <div class="cell"><hr></div>
        <div class="cell center">Find</div>
        <div class="cell">
        <div  id="map_header_wrapper" class="grid-x map_header_wrapper">
              <div id="statistic_drilldown" class="cell medium-6 map_chart_drilldown"></div>
              <div id="map_title_wrapper" class="cell medium-6 map_title_wrapper">
                    <strong id="section_title" class="section_title"></strong><br>
                  <span id="current_level" class="current_level"></span>
              </div>
           </div>
        </div>
        <div class="cell"><hr></div>
        <div class="cell center padding-bottom-1"><h2 id="location_title">World</h2>Population: <span id="location_population">7,600,000,000</span></div>
        <div class="cell center">
            <div class="callout">
                <div class="grid-x">
                    <div class="cell small-2">Trainings</div>
                    <div class="cell small-2">Needed<br>32,000</div>
                    <div class="cell small-2">Complete<br>10,000</div>
                    <div class="cell small-2">Churches</div>
                    <div class="cell small-2">Needed<br>360,000</div>
                    <div class="cell small-2">Formed<br>20,000</div>
                </div>
            </div>
        </div>
    </div>
  `)


  // set the depth of the drill down
  DRILLDOWNDATA.settings.hide_final_drill_down = true
  // load drill down
  DRILLDOWN.get_drill_down('statistic_drilldown')

})

window.DRILLDOWN.statistic_drilldown = function( grid_id ) {
  if ( grid_id !== 'top_map_level' ) { // make sure this is not a top level continent or world request
    console.log(grid_id)
    add_location_snapshot( grid_id )
  }
  else { // top_level maps
    console.log(grid_id)
    add_location_snapshot( grid_id )
  }
}

function add_location_snapshot( grid_id ) {
  let rest = DRILLDOWNDATA.settings.endpoints.get_map_by_grid_id_endpoint

  if ( DRILLDOWNDATA.data[grid_id] ) {
    build_map( DRILLDOWNDATA.data[grid_id] )
  } else {
    jQuery.ajax({
      type: rest.method,
      contentType: "application/json; charset=utf-8",
      data: JSON.stringify( { 'grid_id': grid_id, 'cached': DRILLDOWNDATA.settings.cached, 'cached_length': DRILLDOWNDATA.settings.cached_length } ),
      dataType: "json",
      url: DRILLDOWNDATA.settings.root + rest.namespace + rest.route,
      beforeSend: function(xhr) {
        xhr.setRequestHeader('X-WP-Nonce', rest.nonce);
      },
    })
      .done( function( response ) {
        DRILLDOWNDATA.data[grid_id] = response
        build_stats(response)

      }) // end success statement
      .fail(function (err) {
        console.log("error")
        console.log(err)
      })
  }

  function build_stats(response) {

    jQuery('#location_title').html(response.self.name)
    jQuery('#location_population').html(response.self.population)


    console.log(response)
  }
}





