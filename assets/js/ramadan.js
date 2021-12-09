

window.mapbox_library_api.current_map_type = "area"
jQuery(document).ready(function($) {
   window.mapbox_library_api.get_level = function (){
     return 'world';
   }
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
            if ( link ){
                content_html += `<li>
                    <a target="_blank" href="${window.lodash.escape(link)}">
                        ${window.lodash.escape(initiative.label)} ${window.lodash.escape(initiative.campaign_progress)}
                    </a>
                </li>`
            } else {
                content_html += `<li>${window.lodash.escape(initiative.label)}</li>`
            }
        })
        content_html += `</ul>`;

        let content = $('#geocode-details-content')
        content.html( content_html);
    }
})
