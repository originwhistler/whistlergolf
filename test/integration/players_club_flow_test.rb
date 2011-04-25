require "#{File.dirname(__FILE__)}/../test_helper"

class PlayersClubFlowTest < ActionController::IntegrationTest

  should "hit signup page" do
    visit '/players/new'
    fill_in 'player_email', :with => "aaron#{Time.now}@aaronglenn.ca"
    fill_in 'player_password', :with => 'password'
    click_button 'Sign up'
    follow_redirect!
    # assert_template 'show'
    
  end

end