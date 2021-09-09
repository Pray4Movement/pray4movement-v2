_ = _ || window.lodash
let dtps = dtpsCounters
let stats = dtpsCounters.statistics

console.log(stats)

jQuery(document).ready(function($){

  // World Population
  let pop = $('#population-count-1')
  pop.html(stats.counter[1].calculated_population_year.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","))
  setInterval(function() { // births
    stats.counter[1].calculated_population_year++;
    pop.html(stats.counter[1].calculated_population_year.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","))
  }, stats.counter[1].births_interval);
  setInterval(function() { // deaths
    stats.counter[1].calculated_population_year--;
    pop.html(stats.counter[1].calculated_population_year.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","))
  }, stats.counter[1].deaths_interval);

  // deaths without Christ
  let dwc = $('#christless-deaths-today-count-1')
  dwc.html(stats.counter[1].christless_deaths_today.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","))
  setInterval(function() {
    stats.counter[1].christless_deaths_today++;
    dwc.html(stats.counter[1].christless_deaths_today.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","))
  }, stats.counter[1].christless_deaths_interval);

  let birth_unreached = $('#births-among-unreached-today-count-1')
  birth_unreached.html(stats.counter[1].births_among_unreached_today.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","))
  setInterval(function() {
    stats.counter[1].births_among_unreached_today++;
    birth_unreached.html(stats.counter[1].births_among_unreached_today.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","))
  }, stats.counter[1].births_among_unreached_interval);

  // Trainings
  let trainings = $('#trainings-needed-count-1')
  trainings.html(stats.counter[1].trainings_needed.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","))

  // Churches
  let churches = $('#churches-needed-count-1')
  churches.html(stats.counter[1].churches_needed.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","))

  // births today
  let births_today = $('#births-today-count-1')
  births_today.html(stats.counter[1].births_today.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","))
  setInterval(function() { // births
    stats.counter[1].births_today++;
    births_today.html(stats.counter[1].births_today.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","))
  }, stats.counter[1].births_interval);


  // deaths today
  let deaths_today = $('#deaths-today-count-1')
  deaths_today.html(stats.counter[1].deaths_today.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","))
  setInterval(function() { // deaths
    stats.counter[1].deaths_today++;
    deaths_today.html(stats.counter[1].deaths_today.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","))
  }, stats.counter[1].deaths_interval);

  // population growth today
  let pop_today = $('#population-growth-today-count-1')
  pop_today.html(stats.counter[1].calculated_population_today.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","))
  setInterval(function() { // births
    stats.counter[1].calculated_population_today++;
    pop_today.html(stats.counter[1].calculated_population_today.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","))
  }, stats.counter[1].births_interval);
  setInterval(function() { // deaths
    stats.counter[1].calculated_population_today--;
    pop_today.html(stats.counter[1].calculated_population_today.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","))
  }, stats.counter[1].deaths_interval);


  $('#churches-reported-count-1').html(stats.counter[1].churches_reported)
  $('#trainings-reported-count-1').html(stats.counter[1].trainings_reported)

  // Progress
  $('#live-trainings-reported-count-1').html(stats.counter[1].trainings_reported)
  $('#live-churches-reported-count-1').html(stats.counter[1].churches_reported)

})
