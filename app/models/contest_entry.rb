class ContestEntry < ActiveRecord::Base
  
  validates_presence_of :agree_to_terms
  
  # attr :promotions
  
  def newsletter_list
    cm = CampaignMonitor.new
    CampaignMonitor::List.new('8099c4725519371b6fe32515aa58d8a7')
  end
  
  def after_create()
    
    custom_fields = {
                    :signuptype     => 'ContestEntry',
                    :promotions     => valid_promotions,
                    :telephone      => telephone,
                    :first_name     => first_name,
                    }
    
    # newsletter_list.add_subscriber(email, full_name, custom_fields)  unless TEST_MODE
    Mailer.deliver_contest_entry(self) unless TEST_MODE
  end

  def full_name
    "#{first_name} #{last_name}"
  end
  
  def valid_promotions
    arr = []
    unless promotions.blank?
      arr = promotions.to_a.each{|item| item.gsub('- ', '').chomp! if item}
      arr.shift
    end
    arr
  end
  
end
