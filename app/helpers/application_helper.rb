# Methods added to this helper will be available to all templates in the application.

require 'yaml'

module ApplicationHelper
  
  def course_card_for(hole_number)
    hole = load_hole_data(hole_number)
    render :partial => 'pages/course/hole', :locals => {:hole => hole}
  end
  
  def hole_image_for(hole)
    image_tag "/images/holes/#{hole['number']}.jpg"
  end
  
  def load_hole_data(number)
    data_file = File.expand_path(File.dirname(__FILE__)) + '/../views/pages/course/data.yml'
    data = YAML.load_file(data_file)
    hole_data = data[number]
  end
  
  
  def render_flash_messages
    html = ""
  	["notice", "warning", "error", "failure", "success"].each do |type|
  		unless flash[type.intern].nil?
  			html << content_tag("div", flash[type.intern].to_s,
  				:id => type, :class => "flash #{type}")
  		end
  	end
  	content_tag("div", html, :id => "flash")
  end
  
  def logo_link(image, link)
    link_to(image_tag("/images/logos/#{image.to_s}.gif"), "http://www.#{link}")
  end
  
  def show_admin_content?
    signed_in_as_admin?
  end

  def players_club?
    params[:controller] == 'players'
  end
  
  def passholder?
    params[:controller] == 'passholders'
  end
  
  def players_club_link
    if current_user && current_user.is_a?(Player)
      player_path(current_user) 
    else
      players_path
    end
  end
  
  def dynamic_content(title)
    content = Content.find_by_title(title) || ''
    markdown(content.text) if content
  end
  
  def prepare_attribute_for_post(attribute)
    arr = attribute.to_a.each{|item| item.gsub('- ', '').chomp!}
    arr.shift
    arr
  end
   
   
end
