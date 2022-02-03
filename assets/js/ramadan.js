

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
            let link = window.p4m_ramadan.type === "ramadan" ? ( initiative.campaign_link || initiative.initiative_link ) : ( initiative.initiative_link || initiative.campaign_link );
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


    if ( !window.p4m_ramadan.small ){
        $('#geocode-details').prepend(`
            <div style="margin-bottom: 10px">
            <span style="vertical-align: middle">
                <span style="height:20px;width:20px;border:1px solid;background-color:#FFCCCDFF;display: inline-block;vertical-align: middle"></span>
                Active Initiatives
           </span>
           <span style="vertical-align: middle">
                <span style="height:20px;width:20px;border:1px solid;background-color:rgba(255,200,73,0.35);display: inline-block;vertical-align: middle"></span>
                Planned Initiatives
           </span>
           </div>
        `)
    }
})
