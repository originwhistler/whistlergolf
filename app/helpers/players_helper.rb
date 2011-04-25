module PlayersHelper
  def player_content(title)
    content = Content.find_by_title(title) || ''
    content.text if content
  end
end
