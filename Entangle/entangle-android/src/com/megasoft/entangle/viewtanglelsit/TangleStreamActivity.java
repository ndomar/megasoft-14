package com.megasoft.entangle.viewtanglelsit;

import java.util.ArrayList;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.ListView;

import com.megasoft.config.Config;
import com.megasoft.entangle.CreateTangleActivity;
import com.megasoft.entangle.R;
import com.megasoft.entangle.TangleActivity;
import com.megasoft.requests.GetRequest;

/**
 * The Activity responsible for view the list of tangles the user is in
 * @author MohamedBassem
 *
 */
public class TangleStreamActivity extends Activity {
	
	private SharedPreferences settings;
	private String sessionId;
	/**
	 * An arraylist to map the list items to their id
	 */
	private ArrayList<Integer> tangleIds;
	
	/**
	 * An arraylist to map the list items to their name
	 */
	private ArrayList<String> tangleNames;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_tangle_stream);
        this.settings = getSharedPreferences(Config.SETTING, 0);
		this.sessionId = settings.getString(Config.SESSION_ID, "");
		tangleIds = new ArrayList<Integer>();
		tangleNames = new ArrayList<String>();
		fetchTangles();
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {

		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.tangle_stream, menu);
		return true;
	}

	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
		// Handle action bar item clicks here. The action bar will
		// automatically handle clicks on the Home/Up button, so long
		// as you specify a parent activity in AndroidManifest.xml.
		int id = item.getItemId();
		if (id == R.id.action_settings) {
			return true;
		}
		return super.onOptionsItemSelected(item);
	}
	
	/**
	 * The method responsible for fetching the tangles from the api
	 * @author MohamedBassem
	 */
	private void fetchTangles() {
		GetRequest getRequest = new GetRequest(Config.API_BASE_URL
				+ "/tangle") {
			public void onPostExecute(String response) {
				if(!this.hasError() && this.getStatusCode() == 200){
					showData(response);
				}else{
					// TODO
				}
			}
		};

		getRequest.addHeader(Config.API_SESSION_ID, sessionId);
		getRequest.execute();

	}
	
	/**
	 * The method responsible for populating the list view from the response of the request
	 * @param response
	 * @author MohamedBassem
	 */
	private void showData(String response) {
		ListView listView = (ListView) findViewById(R.id.view_tangle_tangle_titles);
		listView.removeViews(0, listView.getCount());
		tangleIds.clear();
		tangleNames.clear();
		try {
			JSONObject json = new JSONObject(response);
			JSONArray tangles = json.getJSONArray("tangles");
			String[] arr = new String[tangles.length()];
			for(int i=0;i<tangles.length();i++){
				JSONObject tangle = tangles.getJSONObject(i);
				arr[i] = tangle.getString("tangleName"); 
				tangleIds.add(tangle.getInt("id"));
				tangleNames.add(tangle.getString("tangleName"));
			}

			listView.setAdapter(new ArrayAdapter<String>(getApplicationContext(), R.layout.tangle_entry_fragment, R.id.view_tangle_tangle_entry, arr));
			listView.setOnItemClickListener(new ListView.OnItemClickListener() {
				@Override
				public void onItemClick(AdapterView<?> arg0, View view, int position,
						long id) {
					goToTangle(position);
				}
			}); 
		} catch (JSONException e) {
			e.printStackTrace();
		}
	}
	
	/**
	 * The onclick handlers for the each tangle item
	 * @param position The position of the tangle in the list
	 * @author MohamedBassem
	 */
	public void goToTangle(int position){
		Intent intent = new Intent(this,TangleActivity.class);
		intent.putExtra("tangleId", tangleIds.get(position));
		intent.putExtra("tangleName", tangleNames.get(position));
		intent.putExtra("sessionId", sessionId);
		startActivity(intent);		
	}
	
	/**
	 * The onclick handler for the create tangle button
	 * @param view
	 * @author MohamedBassem
	 */
	public void goToCreateTangle(View view){
		startActivity(new Intent(this,CreateTangleActivity.class));
	}

}
