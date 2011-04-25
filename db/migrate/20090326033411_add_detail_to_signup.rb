class AddDetailToSignup < ActiveRecord::Migration
  def self.up
    add_column :sign_ups, :income, :string
    add_column :sign_ups, :other_courses, :string
    add_column :sign_ups, :number_of_lessons, :string
    add_column :sign_ups, :times_per_month, :string
    add_column :sign_ups, :how_did_you_hear, :string
    add_column :sign_ups, :website_rating, :string
    add_column :sign_ups, :influencing_factor, :string
    add_column :sign_ups, :influencing_factor_2, :string
    add_column :sign_ups, :influencing_factor_3, :string
  end

  def self.down
    remove_column :sign_ups, :influencing_factor_3
    remove_column :sign_ups, :influencing_factor_2
    remove_column :sign_ups, :influencing_factor
    remove_column :sign_ups, :website_rating
    remove_column :sign_ups, :how_did_you_hear
    remove_column :sign_ups, :times_per_month
    remove_column :sign_ups, :number_of_lessons
    remove_column :sign_ups, :other_courses
    remove_column :sign_ups, :income
  end
end
