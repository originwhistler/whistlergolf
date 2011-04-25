require 'test_helper'

class PlayersControllerTest < ActionController::TestCase
  fixtures :all
  def setup
    @user = Factory(:admin)
    sign_in_as @user
  end
end
