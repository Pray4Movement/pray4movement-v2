

window.mapbox_library_api.current_map_type = "area"
jQuery(document).ready(function($) {
   window.mapbox_library_api.get_level = function (){
     return 'world';
   }
    console.log(window.p4m_ramadan);
    window.mapbox_library_api.area_map.load_detail_panel = function(location_id, name){
        let location_data = window.p4m_ramadan.data.locations[location_id] || []
        let initiatives = location_data.initiatives || []
        console.log(location_data);
        $('#geocode-details-title').html( name )
        let content_html = `<ul>`
        if ( initiatives.length === 0 ){
            content_html += `<li>No prayer initiatives here yet</li>`
        }
        initiatives.forEach(initiative=>{
            if ( initiative.link){
                content_html += `<li><a href="${window.lodash.escape(initiative.link)}">${window.lodash.escape(initiative.label)}</a></li>`
            } else {
                content_html += `<li>${window.lodash.escape(initiative.label)}</li>`
            }
        })
        content_html += `</ul>`;

        let content = $('#geocode-details-content')
        content.html( content_html);
    }
})
