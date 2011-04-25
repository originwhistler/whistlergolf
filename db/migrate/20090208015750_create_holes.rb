class CreateHoles < ActiveRecord::Migration
  def self.up
    create_table :holes do |t|
      t.integer :number
      t.integer :par
      t.integer :tournament_tees
      t.integer :palmer_tees
      t.integer :white_tees
      t.integer :forward_tees
      t.integer :handicap_mens
      t.integer :handicap_womens
      t.text :description

      t.timestamps
    end
  end

  def self.down
    drop_table :holes
  end
end
