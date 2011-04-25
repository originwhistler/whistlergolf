
class SignUp < ActiveRecord::Base
  
  validates_presence_of :first_name, :last_name, :telephone, :email, :birthdate, :handicap, :company_event, :city, :province, :other_courses, :number_of_lessons, :influencing_factor, :influencing_factor_2, :influencing_factor_3
  # validates_presence_of :timesperyear, "How often do you play"
  attr_accessor :promos
  
  def newsletter_list
    cm = CampaignMonitor.new
    CampaignMonitor::List.new('8099c4725519371b6fe32515aa58d8a7')
  end
  
  def after_create()
    # Add custom data, the model holds all
    custom_fields = { :signuptype => signuptype, 
                      :handicap => handicap, 
                      :timesperyear => timeperyear,
                      :companyevent => company_event,
                      :promotions => valid_promotions,
                      :city => city,
                      :birthdate => birthdate,
                      :telephone => telephone,
                      :province_state => province
                    }
    

    # PHP Example
    # $result = $cm->subscriberAddWithCustomFields('joe@notarealdomain.com', 'Joe Smith', array('Interests' => array('Xbox', 'Basketball'), 'Dog' => 'Fido'));

    # Your existing fields
    # Field Name  Personalization Tag   Data Type
    # 
    # Name  [fullname,fallback=]
    # [firstname,fallback=]
    # [lastname,fallback=]  Text   
    # Email Address   [email]   Text   
    # sign up type (edit)   [signuptype,fallback=]  Multi-Options (select many)   Delete
    # handicap (edit)   [handicap,fallback=]  Text  Delete
    # promotions (edit)   [promotions,fallback=]  Multi-Options (select many)   Delete
    # times per year (edit)   [timesperyear,fallback=]  Text  Delete
    # company event (edit)  [companyevent,fallback=]  Multi-Options (select one)  Delete
    # city (edit)   [city,fallback=]  Text  Delete
    # birthdate (edit)  [birthdate,fallback=]   Text  Delete
    # telephone (edit)  [telephone,fallback=]   Text  Delete
    # first_name (edit)   [first_name,fallback=]  Text  Delete
    # province_state (edit)   [province_state,fallback=]  Text
    
    # newsletter_list.add_subscriber(email, full_name, custom_fields) unless TEST_MODE
    Mailer.deliver_signup(self) unless TEST_MODE
    Mailer.deliver_signup_confirmation(self) unless TEST_MODE
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
