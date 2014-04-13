package com.megasoft.entangle;

import org.json.JSONArray;

import com.megasoft.requests.*;

import android.os.Bundle;
import android.app.Activity;
import android.view.Menu;

public class ViewMessagesActivity extends Activity {
	
	int userId;
	
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_view_messages);
		GetRequest getMessages = new GetRequest("/user/" + userId + "messages");
	}
	void RenderMessages(JSONArray messages){
		
	}
	
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.view_messages, menu);
		return true;
	}

}
