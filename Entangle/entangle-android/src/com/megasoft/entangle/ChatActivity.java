package com.megasoft.entangle;

import com.megasoft.config.Config;

import android.os.Bundle;
import android.app.Activity;
import android.content.SharedPreferences;
import android.view.Menu;
import android.widget.Button;

public class ChatActivity extends Activity {
	public Button send;
	private SharedPreferences settings;
	private String sessionId;
	private int offerId;
	

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_chat);
		this.settings = getSharedPreferences(Config.SETTING, 0);
		this.sessionId = settings.getString(Config.SESSION_ID, "");
	}
	

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.chat, menu);
		return true;
	}

}
