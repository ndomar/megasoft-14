package com.megasoft.entangle;

import com.megasoft.config.Config;

import android.os.Bundle;
import android.app.Activity;
import android.content.SharedPreferences;
import android.view.Menu;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.TextView;

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
		this.setSendButton(findViewById(android.R.id.content));
		chatLayout = (LinearLayout) this.findViewById(R.id.chat);
	}
	/**
	 * this method defines what happens when send button is clicked
	 * @param view  the view of this activity
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
			}
		});
	}
	

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.chat, menu);
		return true;
	}

}
