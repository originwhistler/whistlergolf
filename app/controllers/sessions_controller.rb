class SessionsController < ApplicationController
  include Clearance::App::Controllers::SessionsController
  
  def url_after_create
    if @user.admin
      admin_path
    elsif @user.is_a? Passholder
      passholder_path @user 
    elsif @user.is_a? Player
      player_path @user
    else
      root_url
    end
  end
  
  
end
