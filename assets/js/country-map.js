
window.mapbox_library_api.current_map_type = "area"
jQuery(document).ready(function($) {
   window.mapbox_library_api.get_level = function (){
       let level = 'world'
       if ( mapbox_library_api.map.getZoom() >= 4 ) {
           level = 'admin1'
       }
       return 'admin1'
   }
    window.mapbox_library_api.map.setCenter([-106.24998712991953, 37.28946327585241])
    window.mapbox_library_api.map.setZoom(3.15);
    window.mapbox_library_api.area_map.load_detail_panel = function(location_id, name){
        let location_data = window.p4m_ramadan.data.locations[location_id] || []
        let initiatives = location_data.initiatives || []
        $('#geocode-details-title').html( name )
        let content_html = `<ul>`
        if ( initiatives.length === 0 ){
            content_html += `<li>No prayer initiatives here yet</li>`
        }
        initiatives.forEach(initiative=>{
            let link = initiative.campaign_link || initiative.initiative_link
            if ( initiative.campaign_progress && !isNaN(initiative.campaign_progress)){
                initiative.campaign_progress = ` - ${initiative.campaign_progress}%`
            }
            if ( !initiative.campaign_progress && initiative.status === "forming" ){
                initiative.campaign_progress = ` - Setup in progress`
            }
            if ( link ){
                content_html += `<li>
                    <a target="_blank" href="${window.lodash.escape(link)}">
                        ${window.lodash.escape(initiative.label)} ${window.lodash.escape(initiative.campaign_progress)}
                    </a>
                </li>`
            } else {
                content_html += `<li>${window.lodash.escape(initiative.label)} ${window.lodash.escape(initiative.campaign_progress)}</li>`
            }
        })
        content_html += `</ul>`;

        let content = $('#geocode-details-content')
        content.html( content_html);
    }
    $('#refresh_map_data').on('click', ()=>{
        makeRequest( "POST", mapbox_library_api.obj.settings.totals_rest_url, { refresh: true, type:window.p4m_ramadan.type  } , mapbox_library_api.obj.settings.rest_base_url ).then(()=>{
            location.reload()
        })
    })
    window.mapbox_library_api.area_map.load_layer = async function ( level = null){
        mapbox_library_api.spinner.show()
        // set geocode level, default to auto
        if ( !level ){
            level = mapbox_library_api.get_level()
        }

        let bbox =mapbox_library_api.map.getBounds()

        let data = [{ grid_id:'1', parent_id:'1'}]
        if ( level !== "world" ){
            data = window.p4m_ramadan.data.country_grid_ids
        }

        // default layer to world
        if ( level === 'world' ) {
            data = [{ grid_id:'1', parent_id:'1'}]
        }

        let status404 = window.SHAREDFUNCTIONS.get_json_cookie('geojson_failed', [] )

        let done = []
        data.forEach( res=>{
            let grid_id = res.grid_id
            let parent_id = res.parent_id
            let layer_id = 'dt-maps-' + parent_id.toString()
            // is new test
            if ( !window.lodash.find(area_map.previous_grid_list, {parent_id:parent_id}) && !status404.includes(parent_id) && !done.includes(parent_id) ) {
                // is defined test
                let mapLayer = mapbox_library_api.map.getLayer(layer_id);
                if(typeof mapLayer === 'undefined') {

                    done.push(parent_id);
                    // get geojson collection
                    jQuery.get( mapbox_library_api.obj.settings.map_mirror + 'collection/' + parent_id + '.geojson', null, null, 'json')
                    .done(function (geojson) {
                        // add data to geojson properties
                        let highest_value = 1
                        jQuery.each(geojson.features, function (i, v) {
                            if (area_map.grid_data[geojson.features[i].properties.id]) {
                                geojson.features[i].properties.value = parseInt(area_map.grid_data[geojson.features[i].properties.id].count)
                            } else {
                                geojson.features[i].properties.value = 0
                            }
                            highest_value = Math.max(highest_value,  geojson.features[i].properties.value)
                        })
                        // add source
                        let mapSource = mapbox_library_api.map.getSource(layer_id);
                        if (typeof mapSource==='undefined') {
                            mapbox_library_api.map.addSource(layer_id, {
                                'type': 'geojson',
                                'data': geojson
                            });
                        }

                        // add fill layer
                        mapbox_library_api.map.addLayer({
                            'id': layer_id,
                            'type': 'fill',
                            'source': layer_id,
                            'paint': {
                                'fill-color': {
                                    property: 'value',
                                    stops: [[-1, 'rgba(255,200,73,0.35)'], [0, 'rgba(0, 0, 0, 0)'], [1, 'rgb(255,204,205)'], [highest_value, 'rgb(220,56,34)']]
                                },
                                'fill-opacity': 0.75,
                                'fill-outline-color': '#707070',
                            }
                        }, area_map.behind_layer);
                        // console.log(layer_id);
                        mapbox_library_api.map.on('click', layer_id, e=> {
                            mapbox_library_api.area_map.load_detail_panel(e.features[0].properties.id, e.features[0].properties.name )
                        })
                    }).catch(()=>{
                        status404.push(parent_id)
                        window.SHAREDFUNCTIONS.save_json_cookie( 'geojson_failed', status404, 'metrics' )
                    })// end get geojson collection
                }
            } // end load new layer
        })
        area_map.previous_grid_list.forEach(grid_item=>{
            let layer_id = 'dt-maps-' + grid_item.parent_id
            let mapLayer =mapbox_library_api.map.getLayer(layer_id);
            if(typeof mapLayer !== 'undefined' && !window.lodash.find(data, {parent_id:grid_item.parent_id})) {
                mapbox_library_api.map.removeLayer( layer_id )
                mapbox_library_api.map.removeSource( layer_id )
            }
        })
        area_map.previous_grid_list = data
        mapbox_library_api.spinner.hide()
    }

})
