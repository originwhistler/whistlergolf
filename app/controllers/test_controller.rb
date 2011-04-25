class TestController < ApplicationController
  def cm
    cm = CampaignMonitor.new
    newsletter_list = CampaignMonitor::List.new('8099c4725519371b6fe32515aa58d8a7')
    custom_fields = { :signuptype => 'signup', 
                      :handicap => '1', 
                      :timesperyear => '1',
                      :companyevent => '1',
                      :promotions => '1',
                      :city => 'calgary',
                      :birthdate => '1977',
                      :telephone => '333',
                      :province_state => 'ab'
                    }
    api_call = newsletter_list.add_subscriber('aaron@aaronglenn.ca', 'Aaron Glenn', custom_fields)
    render :text => api_call.to_yaml
  end
end
