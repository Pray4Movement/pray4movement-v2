jQuery(document).ready(function(){
  jQuery(window.location.hash).addClass('highlight-background-hover')

  // Top menu fix for all playbook entries making Resources active class.
  if ( window.location.pathname.split('/')[1] === 'playbook' || window.location.pathname.split('/')[1] === "playbook-categories") {
    jQuery('.menu-item-98').addClass('active')
    jQuery('.menu-item-135').addClass('active')
  }

  // Top menu fix for all articles entries making Resources active class.
  if ( window.location.pathname.split('/')[1] === 'articles' || window.location.pathname.split('/')[1] === "article-topics" ) {
    jQuery('.menu-item-98').addClass('active')
    jQuery('.menu-item-111').addClass('active')
  }

  // Top menu fix for all news entries making Statistics active class.
  if ( window.location.pathname.split('/')[1] === 'news' || window.location.pathname.split('/')[1] === "report-categories" ) {
    jQuery('.menu-item-31').addClass('active')
    jQuery('.menu-item-105').addClass('active')
  }

})

