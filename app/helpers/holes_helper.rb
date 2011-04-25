module HolesHelper
  def front_nine
    Hole.front_nine
  end
  
  def back_nine
    Hole.back_nine
  end
  
  def out_for(type)
    Hole.front_nine.sum(type.to_s)
  end
  
  def in_for(type)
    Hole.back_nine.sum(type.to_s)
  end
  
  def ttl_for(type)
    Hole.back_nine.sum(type.to_s) + Hole.front_nine.sum(type.to_s)
  end
  
  def image_for(hole)
    "/images/holes/preview/#{hole.number}.png"
  end
  
  def detail_image_for(hole)
    # hole.image.url
    "/images/holes/detail/#{hole.number}.png"
  end
  
  def video_for(hole)
    link_to "Video Preview", "/video/whistlerGC#{hole.number}.mov", :class => 'lightwindow page-options', :params => 'lightwindow_width=320,lightwindow_height=260', :rel => 'course'
    <<-eos
    <script type="text/javascript">
		// <![CDATA[
			
			// create the qtobject and write it to the page, this includes plugin detection
			// be sure to add 15px to the height to allow for the controls
			var myQTObject = new QTObject("/video/whistlerGC#{hole.number}.mov", "hole", "320", "255");
			myQTObject.addParam("autostart", "false");
			myQTObject.write();
			
		// ]]>
		</script>
		eos
  end
  
end
