class Contact < ActiveRecord::Base
  def after_create
    Mailer.deliver_contact(self)
  end
end
