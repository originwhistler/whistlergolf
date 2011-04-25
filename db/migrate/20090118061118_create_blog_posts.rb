class CreateBlogPosts < ActiveRecord::Migration
  def self.up
    create_table :blog_posts do |t|
      t.integer :blog_category_id
      t.string :title
      t.date :date
      t.text :content
      t.boolean :active

      t.timestamps
    end
  end

  def self.down
    drop_table :blog_posts
  end
end
