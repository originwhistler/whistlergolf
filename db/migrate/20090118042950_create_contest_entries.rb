class CreateContestEntries < ActiveRecord::Migration
  def self.up
    create_table :contest_entries do |t|
      t.string :first_name
      t.string :last_name
      t.string :telephone
      t.string :email
      t.boolean :group_organizer
      t.boolean :large_group
      t.text :comments
      t.boolean :agree_to_terms

      t.timestamps
    end
  end

  def self.down
    drop_table :contest_entries
  end
end
