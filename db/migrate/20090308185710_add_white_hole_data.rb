class AddWhiteHoleData < ActiveRecord::Migration
  def self.up
    Hole.find(1).update_attributes(:white_tees => 340)
    Hole.find(2).update_attributes(:white_tees => 362)  	
    Hole.find(3).update_attributes(:white_tees => 493)  	
    Hole.find(4).update_attributes(:white_tees => 280)  	
    Hole.find(5).update_attributes(:white_tees => 211)  	
    Hole.find(6).update_attributes(:white_tees => 355)  	
    Hole.find(7).update_attributes(:white_tees => 363)  	
    Hole.find(8).update_attributes(:white_tees => 161)  	
    Hole.find(9).update_attributes(:white_tees => 478)  
    Hole.find(10).update_attributes(:white_tees => 301)  	
    Hole.find(11).update_attributes(:white_tees => 486)  	
    Hole.find(12).update_attributes(:white_tees => 378)  	
    Hole.find(13).update_attributes(:white_tees => 358)  	
    Hole.find(14).update_attributes(:white_tees => 150)  	
    Hole.find(15).update_attributes(:white_tees => 352)  	
    Hole.find(16).update_attributes(:white_tees => 450)  	
    Hole.find(17).update_attributes(:white_tees => 150)  	
    Hole.find(18).update_attributes(:white_tees => 336)
  end

  def self.down
  end
end
