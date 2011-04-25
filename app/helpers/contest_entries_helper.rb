module ContestEntriesHelper
  
  def contest_home_title
    content = Content.find_by_title('Contest Callout')
    if content
      content.text
    else
     "<span>2 ROUNDS</span> OF GOLF FOR 4 PEOPLE"
    end
  end
  
  def contest_title
    content = Content.find_by_title('Contest Title')
    if content
      content.text
    else
      "Can't find Content"
    end
  end
  
  def contest_description
    content = Content.find_by_title('Content Description')
    if content
      content.text
    else
      "Can't find Content"
    end
  end
end
