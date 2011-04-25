class AddWhiteTeeData < ActiveRecord::Migration
  def self.up
    data = %w(340 362 493 280 211 355 363 161 478 301 486 378 358 150 352 450 150 336)
    
    Hole.find(:all, :order => :number).each do |hole|
      hole.white_tees = data[hole.number - 1]
      hole.save
    end
    
  end

  def self.down
  end
end
