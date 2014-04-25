package com.megasoft.entangle.viewtanglelsit;

import java.util.ArrayList;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.ListView;
import android.widget.Toast;

import com.megasoft.config.Config;
import com.megasoft.entangle.CreateTangleActivity;
import com.megasoft.entangle.HomeActivity;
import com.megasoft.entangle.R;
import com.megasoft.entangle.TangleActivity;
import com.megasoft.requests.GetRequest;

/**
 * The Activity responsible for view the list of tangles the user is in
 * @author MohamedBassem
 *
 */
public class TangleStreamActivity extends Fragment {
	
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
	
	private View view;
	
	private HomeActivity activity;
	
	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
            Bundle savedInstanceState) {
		
		view = inflater.inflate(R.layout.activity_tangle_stream, container, false);
		
        this.settings = activity.getSharedPreferences(Config.SETTING, 0);
		this.sessionId = settings.getString(Config.SESSION_ID, "");
		tangleIds = new ArrayList<Integer>();
		tangleNames = new ArrayList<String>();
		fetchTangles();
		
		return view;
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
					showErrorMessage();
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
		ListView listView = (ListView) view.findViewById(R.id.view_tangle_tangle_titles);
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

			listView.setAdapter(new ArrayAdapter<String>(activity.getApplicationContext(), R.layout.sidebar_list_item, R.id.textView1, arr));
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
		activity.switchFragment(tangleIds.get(position), position);
	}
	
	/**
	 * The onclick handler for the create tangle button
	 * @param view
	 * @author MohamedBassem
	 */
	public void goToCreateTangle(View view){
		startActivity(new Intent(activity,CreateTangleActivity.class));
	}
	
	/**
	 * A method to show an error toast.
	 * @author MohamedBassem
	 */
	public void showErrorMessage(){
		Toast.makeText(activity, "Something went wrong", Toast.LENGTH_LONG).show();
	}
	
	@Override
	public void onAttach(Activity activity) {
		this.activity = (HomeActivity) activity;
		super.onAttach(activity);
	}

}
