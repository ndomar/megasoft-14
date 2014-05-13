package com.megasoft.entangle;

import org.json.JSONArray;
import org.json.JSONException;
import com.megasoft.config.Config;
import com.megasoft.requests.GetRequest;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.support.v4.app.FragmentActivity;
import android.view.Menu;
import android.view.View;
import android.widget.LinearLayout;
import android.widget.ScrollView;
import android.widget.Toast;

/**
 * This is the activity of the Notification Stream
 * @author Mohamed Ayman
 *
 */
public class NotificationStream extends FragmentActivity {
	
	/**
	 * The session Id
	 */
	private String sessionId;
	
	/**
	 * The user Id
	 */
	private int loggedInId;
	
	/**
	 * The preferences instance
	 */
	private SharedPreferences settings;
	
	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_notification);
		this.settings = getSharedPreferences(Config.SETTING, 0);
		this.sessionId = settings.getString(Config.SESSION_ID, "");
		this.loggedInId = settings.getInt(Config.USER_ID, 1);
		generate();
	}
	
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		getMenuInflater().inflate(R.menu.main, menu);
		return true;
	}

	/**
	 * This method creates GET request to retrive the notifications of the user
	 * @author Mohamed Ayman
	 */
	public void generate() {
		
		String link = Config.API_BASE_URL + "/user/" + loggedInId + "/notifications";
		
		GetRequest request = new GetRequest(link) {
			@Override
			protected void onPostExecute(String response) {
				
				if (this.getStatusCode() == 200) {
					try {
						JSONArray json = new JSONArray(response);
						viewNotifications(json);
						//viewNotificationsTest();
					} catch (JSONException e) {
						e.printStackTrace();
					}
				} else {
					Toast toast = Toast
							.makeText(
									getApplicationContext(),
									this.getStatusCode() + "Error "
											+ this.getStatusCode(),
									Toast.LENGTH_SHORT);
					toast.show();
				}
			}
		};
		request.addHeader("X-SESSION-ID", this.sessionId);
		request.execute();
	}

	/**
	 * This method view the notifications by building fragment for each notification
	 * @param notifications
	 * @author Mohamed Ayman
	 */
	public void viewNotifications(JSONArray notifications) {
		
		LinearLayout notificationsArea = ((LinearLayout) findViewById(R.id.notification_stream));
		notificationsArea.removeAllViews();
		notificationsArea.setVisibility(View.VISIBLE);
		
		for(int i = 0; i < notifications.length();i++) {
			try {
				JSONArray notification = (JSONArray) notifications.getJSONArray(i);
				NotificationStreamFragment fragment = new NotificationStreamFragment();
				String notificationDescription = notification.getString(0);
				int notificationId = notification.getInt(1);
				boolean seen = notification.getBoolean(3);
				String notificationDate = notification.getString(2);
				String linkTo = notification.getString(4);
				fragment.setData(notificationId , seen , notificationDescription , notificationDate , linkTo);
				getSupportFragmentManager().beginTransaction().add(R.id.notification_stream, fragment).commit();
			} catch (JSONException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
			
		}
		
		final ScrollView scrollView = (ScrollView) findViewById(R.id.scroll);
		scrollView.postDelayed(new Runnable() {

			@Override
			public void run() {
				scrollView.fullScroll(ScrollView.FOCUS_UP);
			}
		}, 500);
	}
}