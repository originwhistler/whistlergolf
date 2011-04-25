class AddAttachmentsImageToHole < ActiveRecord::Migration
  def self.up
    add_column :holes, :image_file_name, :string
    add_column :holes, :image_content_type, :string
    add_column :holes, :image_file_size, :integer
  end

  def self.down
    remove_column :holes, :image_file_name
    remove_column :holes, :image_content_type
    remove_column :holes, :image_file_size
  end
end
