class AdminController < ApplicationController
  layout 'admin'
  before_filter :admin_only
  
  def dashboard
    @content = Content.find(:all, :order => 'title')
    @players = Player.find(:all, :order => 'email ASC')
    @passholders = Passholder.find(:all, :order => 'email ASC')
    @wedding_events = WeddingEvent.find(:all, :order => 'created_at DESC')
    @group_outings = GroupOuting.find(:all, :order => 'created_at DESC')
    @contest_entries = ContestEntry.find(:all, :order => 'created_at DESC')
    @signups = SignUp.find(:all, :order => 'created_at DESC')
  end
  
  def method_name
    
  end
  
  def import_holes
    # data_file = File.expand_path(File.dirname(__FILE__)) + '/../views/pages/course/data.yml'
    # data = YAML.load_file(data_file)
    # text = []
    # data.each_with_index do |hole,i|
    #   Hole.create(
    #       :number           => hole[1]['number'],
    #       :par              => hole[1]['par'],
    #       :tournament_tees  => hole[1]['tournament_tees'],
    #       :palmer_tees      => hole[1]['palmer_tees'],
    #       :white_tees       => hole[1]['white_tees'],
    #       :forward_tees     => hole[1]['forward_tees'],
    #       :handicap_mens    => hole[1]['mens_handicap'],
    #       :handicap_womens  => hole[1]['womens_handicap'],
    #       :description      => hole[1]['description']
    #   )
    #   # text << hole[1]['par']
    # end
    # render :text => Hole.all.to_yaml
    # # render :text => text.to_yaml
  end

end
