package com.megasoft.entangle;

import java.util.Calendar;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.config.Config;
import com.megasoft.requests.GetRequest;
import com.megasoft.requests.PostRequest;

import android.os.Bundle;
import android.app.Activity;
import android.content.SharedPreferences;
import android.view.Menu;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

public class ChatActivity extends Activity {
	public Button send;
	public EditText messageBox;
	private SharedPreferences settings;
	private String sessionId;
	private int offerId;
	private ChatActivity current = this;
	private LinearLayout chatLayout;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_chat);
		this.settings = getSharedPreferences(Config.SETTING, 0);
		this.sessionId = settings.getString(Config.SESSION_ID, "");
		send = (Button) this.findViewById(R.id.send);
		messageBox = (EditText) this.findViewById(R.id.message);
		chatLayout = (LinearLayout) this.findViewById(R.id.chat);
		this.setSendButton(findViewById(android.R.id.content));
		this.getChat();
	}
	
	/**
	 * this method defines what happens when send button is clicked
	 * @param view  the view of this activity
	 * @return none
	 * @author Nader Nessem
	 */
	private void setSendButton(View view) {
		send.setOnClickListener(new View.OnClickListener() {

			@Override
			public void onClick(View v) {
				String sentMessage = messageBox.getText().toString();
				TextView message= new TextView(current);
				message.setText(sentMessage);
				chatLayout.addView(message);
				JSONObject json = new JSONObject();
				try {
					//how to know the sender id
					json.put("senderId", 123);
					json.put("offerId",offerId);
					json.put("body", sentMessage);
					json.put("date", Calendar.getInstance().getTime());
				} catch (JSONException e) {
					e.printStackTrace();
				}
				PostRequest sendMessageRequest = new PostRequest(Config.API_BASE_URL + "/offer/" + 
				offerId + "/message"){
		            protected void onPostExecute(String response) { 
		                 if( this.getStatusCode() == 201 ){
		                    Toast sucessToast =Toast.makeText(current,"Message sent Sucessfully"
		                    		, Toast.LENGTH_SHORT);
		                    sucessToast.show();
		                 }
		                 else {
		                	  Toast failToast =Toast.makeText(current,"Unable to send the message"
		                			  , Toast.LENGTH_SHORT);
		                	  failToast.show();
		                  }
		             }
		        };
		        sendMessageRequest.setBody(json); 
		        sendMessageRequest.addHeader("X-SESSION-ID", sessionId); 
		        sendMessageRequest.execute();
			}
		});
	}
	/**
	 * gets the previous chat between the requester and the offerer
	 * @param none
	 * @return none
	 * @author Nader Nessem
	 */
	private void getChat(){
		GetRequest getChatRequest = new GetRequest(Config.API_BASE_URL + "/offer/" + 
				offerId + "/message"){
			 protected void onPostExecute(String response){
				 if( this.getStatusCode() == 200){
					 try {
						JSONObject jsonObject = new JSONObject(response);
						JSONArray jsonArray = jsonObject.getJSONArray("Messages");
						for(int i = 0;i < jsonArray.length();i++){
							JSONObject loopObject = jsonArray.getJSONObject(i);
							String displayMessage = loopObject.getString("senderName");
							displayMessage.concat(loopObject.getString("body"));
							displayMessage.concat("\n sent on" + loopObject.getString("body"));
							TextView text = new TextView(current);
							text.setText(displayMessage);
							chatLayout.addView(text);
						}
					} catch (JSONException e) {
						e.printStackTrace();
					}
				 }
				 else {
					 Toast failToast =Toast.makeText(current,"Unable to view chat,please try again"
               			  , Toast.LENGTH_SHORT);
               	  failToast.show();
				 }
	                   
			 }
			 
		};
		getChatRequest.addHeader("X-SESSION-ID", sessionId); 
		getChatRequest.execute();
	}
	
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.chat, menu);
		return true;
	}

}
