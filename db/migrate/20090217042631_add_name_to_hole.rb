class AddNameToHole < ActiveRecord::Migration
  def self.up
    Hole.find_or_create_by_number(1).update_attribute('name', 'Cedar Grove')
    Hole.find_or_create_by_number(2).update_attribute('name', 'Double Trouble')
    Hole.find_or_create_by_number(3).update_attribute('name', 'Hairpin')
    Hole.find_or_create_by_number(4).update_attribute('name', 'Easy Does It')
    Hole.find_or_create_by_number(5).update_attribute('name', 'Whistler Bowl')
    Hole.find_or_create_by_number(6).update_attribute('name', "Groomer's Choice")
    Hole.find_or_create_by_number(7).update_attribute('name', "Deception Pass")
    Hole.find_or_create_by_number(8).update_attribute('name', "Singing Cedars")
    Hole.find_or_create_by_number(9).update_attribute('name', "Rainbow's Run")
    Hole.find_or_create_by_number(10).update_attribute('name', "Windtaker")
    Hole.find_or_create_by_number(11).update_attribute('name', "Arnie's Eagle")
    Hole.find_or_create_by_number(12).update_attribute('name', "Fade Away")
    Hole.find_or_create_by_number(13).update_attribute('name', "Bear Island")
    Hole.find_or_create_by_number(14).update_attribute('name', "Peak Peak")
    Hole.find_or_create_by_number(15).update_attribute('name', "The Lodge")
    Hole.find_or_create_by_number(16).update_attribute('name', "The Gallery")
    Hole.find_or_create_by_number(17).update_attribute('name', "Bird Bath")
    Hole.find_or_create_by_number(18).update_attribute('name', "Last Run")
  end

  def self.down
    remove_column :holes, :name
  end
end
