class AddEmploymentContent < ActiveRecord::Migration
  def self.up
    Content.create(:title => 'Employment Content', :text => "
    
      ## Job Listings
      
      ### Job 1
      
      This is a description
      
      * List item
      * List item
      * List item
      
      (Link Text)[mailto:aaron@aaronglenn.ca]
    
    ")
  end

  def self.down
  end
end
