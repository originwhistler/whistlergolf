ActionController::Routing::Routes.draw do |map|
  map.resources :lesson_bookings



  map.resources :users, :has_one => [:password, :confirmation], :collection => {:login_status => :post}
  map.resources :passwords

  map.resource :session
  map.logout '/logout', :controller => 'sessions', :action => 'destroy'
  map.login '/login', :controller => 'sessions', :action => 'new'
  map.register '/register', :controller => 'users', :action => 'create'
  map.signup '/signup', :controller => 'users', :action => 'new'

  map.connect '/passholders.asp', :controller => 'passholders', :action => 'index', :template => 'index'
  map.resources :passholders

  
  map.resources :players
  map.players_club '/pc/:action/:id', :controller => 'players'

  map.resources :holes
  
  map.resources :wedding_events
  map.resources :group_outings
  
  map.resources :contacts
  map.contact '/contact', :controller => 'pages', :action => 'contact'

  map.resources :contest_entries
  map.resources :sign_ups

  map.resources :contents
  map.admin '/admin', :controller => 'admin', :action => 'dashboard'  
  
  # The priority is based upon order of creation: first created -> highest priority.
  map.root :controller => 'pages', :action => 'home'
  # Install the default routes as the lowest priority.
  map.connect ':controller/:action/:id'
  map.connect ':controller/:action/:id.:format'
  
  map.connect '*path', :controller => 'pages', :action => 'static'
end
