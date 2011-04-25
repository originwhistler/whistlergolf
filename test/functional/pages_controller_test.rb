require 'test_helper'

class PagesControllerTest < ActionController::TestCase
  fixtures :all
  def setup
    @user = Factory(:admin)
    sign_in_as @user
  end
  
  should "point index of site at pages/home" do
    get 'home'
    assert_response :success
    assert_template 'pages/home'
  end
  
  should "render any file that matches a file in pages views dir" do
    get '/rates'
    assert_response :success
    assert_template 'pages/rates'
    assert_select 'h1', "2009 RATES"
  end

  should "render any path that matches a file in pages views" do
    get '/course/hole-by-hole'
    assert_response :success
    assert_template 'course/hole-by-hole'
    assert_select 'h1', "Hole By Hole"
  end
  
  should "allow in view instance var to set the custom title" do
    get '/rates'
    assert_select 'title', /Rates/
  end
  
end
