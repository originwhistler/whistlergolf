class Mailer < ActionMailer::Base
  def whistler_recipient
    # 'steve@whistler.com, steve@whistlergolf.com, stoulch@tourismwhistler.com, ro@whistlergolf.com, aaron@aaronglenn.ca, rory@owstudios.com'
    'akristma@tourismwhistler.com, rory@owstudios.com, rdavies@tourismwhistler.com, aaron@aaronglenn.ca'
    # 'sebalamy@gmail.com'
  end
  
  def admin_recipient
    'feedback@whistlergolf.com, aaron@aaronglenn.ca'
  end

  def contact(contact, sent_at = Time.now)
    subject    'Contact Email'
    recipients whistler_recipient
    from       DO_NOT_REPLY
    sent_on    sent_at
    
    body       :contact => contact
    content_type 'text/html'    
  end

  def signup(signup, sent_at = Time.now)
    subject    'Whistler Golf Club - Signup'
    recipients admin_recipient
    from       DO_NOT_REPLY
    sent_on    sent_at
    body       :signup => signup
    content_type 'text/html'
  end  

  def contest_entry(contest_entry, sent_at = Time.now)
    subject    'Whistler Golf Club - Contest Entry'
    recipients admin_recipient
    from       DO_NOT_REPLY
    sent_on    sent_at
    body       :contest_entry => contest_entry
    content_type 'text/html'    
  end
  
  def group_outing(group_outing, sent_at = Time.now)
    subject    'Whistler Golf Club - Group Outing'
    recipients whistler_recipient
    from       DO_NOT_REPLY
    sent_on    sent_at
    body       :group_outing => group_outing
    content_type 'text/html'
  end

  def wedding_event(wedding_event, sent_at = Time.now)
    subject    'Whistler Golf Club - Wedding/Event Confirmation'
    recipients whistler_recipient
    from       DO_NOT_REPLY
    sent_on    sent_at
    body       :wedding_event => wedding_event
    content_type 'text/html'
  end

  def lesson_booking(lesson_booking, sent_at = Time.now)
    subject    'Whistler Golf Club - Lesson Booking Confirmation'
    recipients whistler_recipient
    from       DO_NOT_REPLY
    sent_on    sent_at
    body       :lesson_booking => lesson_booking
    content_type 'text/html'
  end



  

  def group_outing_confirmation(group_outing, sent_at = Time.now)
    subject    'Whistler Golf Club - Group Confirmation'
    recipients group_outing.email
    from       DO_NOT_REPLY
    sent_on    sent_at
    body       :group_outing => group_outing, :email_message => Content.find_by_title('Group Outing Confirmation Email')
    content_type 'text/html'
  end

  def wedding_event_confirmation(wedding_event, sent_at = Time.now)
    subject    'Whistler Golf Club - Wedding/Event Confirmation'
    recipients wedding_event.email
    from       DO_NOT_REPLY
    sent_on    sent_at
    body       :wedding_event => wedding_event, :email_message => Content.find_by_title('Wedding Event Confirmation Email')
    content_type 'text/html'
  end

  def player_confirmation(player, sent_at = Time.now)
    subject    'Whistler Golf Club - Players Club Sign Up Confirmation'
    recipients player.email
    from       DO_NOT_REPLY
    sent_on    sent_at
    body       :player => player, :email_message => Content.find_by_title('Player Confirmation Email')
    content_type 'text/html'
  end

  def passholder_confirmation(passholder, sent_at = Time.now)
    subject    'Whistler Golf Club - Passholder Sign Up Confirmation'
    recipients passholder.email
    from       DO_NOT_REPLY
    sent_on    sent_at
    body       :passholder => passholder, :email_message => Content.find_by_title('Passholder Confirmation Email')
    content_type 'text/html'
  end

  def lesson_booking_confirmation(lesson_booking, sent_at = Time.now)
    subject    'Whistler Golf Club - Lesson Booking Confirmation'
    recipients lesson_booking.email
    from       DO_NOT_REPLY
    sent_on    sent_at
    body       :lesson_booking => lesson_booking
    content_type 'text/html'
  end
  
  def signup_confirmation(signup, sent_at = Time.now)
    subject    'Whistler Golf Club - Sign Up Confirmation'
    recipients signup.email
    from       DO_NOT_REPLY
    sent_on    sent_at
    body       :signup => signup, :email_message => Content.find_by_title('Signup Auto Reply Email')
    content_type 'text/html'
  end
  
  def test(sent_at = Time.now)
    subject    'Whistler Golf Club - Test'
    recipients 'aaron@aaronglenn.ca'
    from       DO_NOT_REPLY
    sent_on    sent_at
    body       :test => 'Hlllo'
    content_type 'text/html'
  end

end
