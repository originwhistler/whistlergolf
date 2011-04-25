class AddPassholderLinkContent < ActiveRecord::Migration
  def self.up
    Content.create(:title => 'Passholder Booking Link', :text => "http://www.linkswitch.com")
  end

  def self.down
  end
end
