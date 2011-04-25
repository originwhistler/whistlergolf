class ActiveRecord::Base
  def to_email
    "<pre>#{self.to_yaml}</pre>"
  end
end