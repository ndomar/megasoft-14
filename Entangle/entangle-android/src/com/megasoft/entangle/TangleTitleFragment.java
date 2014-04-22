package com.megasoft.entangle;

import java.util.HashMap;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.app.Fragment;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.support.v4.app.FragmentActivity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;

import com.megasoft.config.Config;
import com.megasoft.requests.GetRequest;

public class TangleTitleFragment extends Fragment {

	private View view;
	private SharedPreferences settings;
	private String sessionId;
	private HashMap<Integer, Integer> ids;
	
	@Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
		
        view = inflater.inflate(R.layout.fragment_tangle_titles, container, false);
        this.settings = getActivity().getSharedPreferences(Config.SETTING, 0);
		this.sessionId = settings.getString(Config.SESSION_ID, "");
		ids = new HashMap<Integer, Integer>();
        fetchTangles();
        return view;
    }
	
	
	
	private void fetchTangles() {
		GetRequest getRequest = new GetRequest(Config.API_BASE_URL
				+ "/tangle") {
			public void onPostExecute(String response) {
				showData(response);
			}
		};

		getRequest.addHeader(Config.API_SESSION_ID, sessionId);
		getRequest.execute();
		
	}

	private void showData(String response) {
		ListView listView = (ListView) view.findViewById(R.id.tangle_titles);
		listView.removeViews(0, listView.getCount());
		
		try {
			JSONObject json = new JSONObject(response);
			JSONArray tangles = json.getJSONArray("tangles");
			String[] arr = new String[tangles.length()];
			for(int i=0;i<tangles.length();i++){
				JSONObject tangle = tangles.getJSONObject(i);
				
				arr[i] = tangle.getString("tangleName");
				ids.put(i,tangle.getInt("id"));
			}
			
			listView.setAdapter(new ArrayAdapter<String>(getActivity().getApplicationContext(), R.layout.sidebar_list_item, R.id.textView1, arr));
			listView.setOnItemClickListener(new ListView.OnItemClickListener() {
				@Override
				public void onItemClick(AdapterView<?> arg0, View view, int position,
						long id) {
//					int tangleId = 1;
//					switchFragment(tangleId, position);		
				}
			}); 
		} catch (JSONException e) {
			e.printStackTrace();
		}
	}

	
}
