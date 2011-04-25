class Hole < ActiveRecord::Base
  
  has_attached_file :image, :styles => {:small => '200x200>', :large => '600x600>'}
  
  
  named_scope :front_nine, :conditions => 'number <= 9', :order => 'number ASC'
  named_scope :back_nine, :conditions => 'number > 9', :order => 'number ASC'
  
  def video
    "/video/holes/hole_#{number}.mov"
  end
  
  
end
