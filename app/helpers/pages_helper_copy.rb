require 'feed_tools'

module PagesHelper
    
  def page_title
    title = @page_title if @page_title
    " - #{title}" if title
  end
   
  def page_css_class
      css_class = ''
      css_class << @page_class if @page_class
      css_class << params[:path].join('-') if params[:path]
      css_class
  end
  
  def section_page
      section = page_css_class
      section = section.split('-')
      section[0]
  end
   
  def testimonial(quote, cite = '')
    "<blockquote><span class='open_quote'></span>#{quote}<span class='close_quote'></span><cite>#{cite}</cite></blockquote>"
  end
  
  def latest_news_from_blog
    blog_feed = FeedTools::Feed.open('http://blog.whistlergolf.com/category/news/feed/')
    items = blog_feed.items
    if items && items.size > 0
      first_item = blog_feed.items.first
      html = link_to first_item.title, first_item.link, :class => 'news_title'
      html << "<br />"
      html << "<span class='feed_description'>#{truncate(first_item.description, 90)}</span>"
    else
      html = 'The Blog Feed is unavailable.'
    end
  end
    
  def generate_js_banner (zoneid,source)
    "var m3_u = (location.protocol=='https:'?'https://d1.openx.org/ajs.php':'http://d1.openx.org/ajs.php');
    var m3_r = Math.floor(Math.random()*99999999999);
    if (!document.MAX_used) document.MAX_used = ',';
    document.write (\"<scr\"+\"ipt type='text/javascript' src='\"+m3_u);
    document.write (\"?zoneid=#{zoneid}&amp;source=#{source}\");
    document.write ('&amp;cb=' + m3_r);
    if (document.MAX_used != ',') document.write (\"&amp;exclude=\" + document.MAX_used);
    document.write (document.charset ? '&amp;charset='+document.charset : (document.characterSet ? '&amp;charset='+document.characterSet : ''));
    document.write (\"&amp;loc=\" + escape(window.location));
    if (document.referrer) document.write (\"&amp;referer=\" + escape(document.referrer));
    if (document.context) document.write (\"&context=\" + escape(document.context));
    if (document.mmm_fo) document.write (\"&amp;mmm_fo=1\");
    document.write (\"'><\/scr\"+\"ipt>\");"
  end
  
  def twitter_feed
    'jQuery(document).ready(function($) {
        $(".tweet").tweet({
          join_text: "auto",
          username: "whistlergolf",
          avatar_size: null,
          count: 2,
          auto_join_text_default: "-", 
          auto_join_text_ed: "-",
          auto_join_text_ing: "-",
          auto_join_text_reply: "-",
          auto_join_text_url: "-",
          loading_text: "loading tweets..."
        });
      })'
  end
  
  
  def getAccordion
    if params[ :acc ]
      "document.observe(\"dom:loaded\", function(){accordion = new Accordion(\"programs-accordion\", #{params[ :acc ]});})"
    else
      "document.observe(\"dom:loaded\", function(){accordion = new Accordion(\"programs-accordion\", 7);})"
    end
  end
  
  def mobile_detect
    'if (jQuery.browser.mobile){window.location.replace("http://m.whistlergolf.com");}'
  end
  
end
